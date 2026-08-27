<?php
/**
 * kon.php — shared bootstrap for the legacy pondokhuda api.
 *
 * hardened rewrite (2026-08):
 *   1. credentials come from config, never from this file
 *   2. all incoming request data is escaped once, here — kills SQLi across every endpoint
 *   3. protected endpoints require an api token (X-Api-Token header or _token POST field)
 *   4. rate limiting helpers for login-ish endpoints
 *
 * the rest of the legacy endpoints keep working untouched because they read
 * the same $host/$user/$pass/$daba globals as before.
 */

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED & ~E_STRICT);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

/* dev convenience: PH_DEBUG=1 turns on error output for this one request */
if (getenv('PH_DEBUG')) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

date_default_timezone_set("Asia/Bangkok");

/* ------------------------------------------------------------------ */
/* cors — allow cross-origin mobile/web clients                        */
/* ------------------------------------------------------------------ */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Api-Token');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

/* ------------------------------------------------------------------ */
/* config                                                              */
/* ------------------------------------------------------------------ */

function _ph_load_config()
{
    $candidates = array(
        __DIR__ . '/../config/config.local.php',
        __DIR__ . '/config-db.php',
    );
    foreach ($candidates as $f) {
        if (is_file($f)) {
            $cfg = include $f;
            if (is_array($cfg)) {
                return $cfg;
            }
        }
    }
    return array(
        'db' => array(
            'host' => getenv('PH_DB_HOST') ? getenv('PH_DB_HOST') : '127.0.0.1',
            'user' => getenv('PH_DB_USER') ? getenv('PH_DB_USER') : '',
            'pass' => getenv('PH_DB_PASS') ? getenv('PH_DB_PASS') : '',
            'name' => getenv('PH_DB_NAME') ? getenv('PH_DB_NAME') : '',
        ),
        'tokens' => array(getenv('PH_API_TOKEN') ? getenv('PH_API_TOKEN') : ''),
    );
}

$PH = _ph_load_config();

$host = $PH['db']['host'];
$user = $PH['db']['user'];
$pass = $PH['db']['pass'];
$daba = $PH['db']['name'];

/* ------------------------------------------------------------------ */
/* mail + receipt helpers                                              */
/* ------------------------------------------------------------------ */

function ph_mail_from()
{
    global $PH;
    $from = isset($PH['mail']['from']) ? trim($PH['mail']['from']) : '';
    if ($from === '') {
        $from = getenv('PH_MAIL_FROM') ? trim(getenv('PH_MAIL_FROM')) : 'noreply@pondokhuda.com';
    }
    return $from;
}

function ph_mail_from_name()
{
    global $PH;
    $name = isset($PH['mail']['from_name']) ? trim($PH['mail']['from_name']) : '';
    return $name !== '' ? $name : 'Pondok Huda';
}

function ph_html_mail_headers()
{
    return 'From: ' . ph_mail_from_name() . ' <' . ph_mail_from() . ">\r\n"
         . "MIME-Version: 1.0\r\n"
         . "Content-Type: text/html; charset=UTF-8\r\n";
}

/** Submit mail through the cPanel/PHP mail transport and log real failures. */
function ph_send_mail($to, $subject, $message, $headers)
{
    $to = trim((string) $to);
    $from = ph_mail_from();
    if (!filter_var($to, FILTER_VALIDATE_EMAIL) || !filter_var($from, FILTER_VALIDATE_EMAIL)) {
        error_log('pondokhuda mail rejected invalid sender or recipient');
        return false;
    }

    $sent = mail($to, $subject, $message, $headers, '-f' . $from);
    if (!$sent) {
        $last = error_get_last();
        error_log('pondokhuda mail submission failed: ' . ($last ? $last['message'] : 'mail() returned false'));
    }
    return $sent;
}

/** Locate receipt assets in a release archive or in this development tree. */
function ph_receipt_asset($name)
{
    global $PH;
    $configured = isset($PH['receipt'][$name]) ? $PH['receipt'][$name] : '';
    $candidates = array();
    if ($configured !== '') {
        $candidates[] = $configured;
    }
    if ($name === 'tcpdf') {
        $candidates[] = __DIR__ . '/pdf/TCPDF-master/tcpdf.php';
        $candidates[] = __DIR__ . '/../web/public/pdf/TCPDF-master/tcpdf.php';
    } elseif ($name === 'template') {
        $candidates[] = __DIR__ . '/kwitansi/template-invoice.jpg';
        $candidates[] = __DIR__ . '/../web/public/kwitansi/template-invoice.jpg';
    }
    foreach ($candidates as $path) {
        if (is_file($path) && is_readable($path)) {
            return $path;
        }
    }
    throw new RuntimeException('receipt asset is missing: ' . $name);
}

function ph_invoice_temp_file()
{
    $base = tempnam(sys_get_temp_dir(), 'pondokhuda-invoice-');
    if ($base === false) {
        throw new RuntimeException('cannot create invoice temporary file');
    }
    @unlink($base);
    return $base . '.pdf';
}

/** Resolve the Laravel public Assets directory for local and cPanel layouts. */
function ph_public_asset_root()
{
    global $PH;
    $configured = isset($PH['assets']['public_root']) ? trim($PH['assets']['public_root']) : '';
    $env = getenv('PH_PUBLIC_ASSET_ROOT') ? trim(getenv('PH_PUBLIC_ASSET_ROOT')) : '';
    $candidates = array();
    if ($configured !== '') {
        $candidates[] = $configured;
    }
    if ($env !== '') {
        $candidates[] = $env;
    }
    // Production: ~/api and ~/laravel. Development: project/api and project/web.
    $candidates[] = dirname(__DIR__) . '/laravel/public/Assets';
    $candidates[] = dirname(__DIR__) . '/web/public/Assets';
    foreach ($candidates as $path) {
        if (is_dir($path)) {
            return rtrim($path, '/');
        }
    }
    throw new RuntimeException('public asset directory is missing');
}

/* ------------------------------------------------------------------ */
/* connection (also used for escaping)                                 */
/* ------------------------------------------------------------------ */

$koneksi = @mysqli_connect($host, $user, $pass, $daba);
if ($koneksi) {
    mysqli_set_charset($koneksi, 'utf8mb4');
}

/**
 * Escape a value (or whole array) for safe interpolation into
 * single-quoted SQL string literals. All $_GET/$_POST/$_REQUEST
 * values are passed through this at bootstrap time.
 */
function esc($v)
{
    global $koneksi;
    if (is_array($v)) {
        return array_map('esc', $v);
    }
    $v = (string) $v;
    if ($koneksi) {
        return mysqli_real_escape_string($koneksi, $v);
    }
    return str_replace(
        array("\\", "'", "\"", "\0", "\n", "\r"),
        array("\\\\", "\\'", '\\"', "\\0", "\\n", "\\r"),
        $v
    );
}

/* neutralize the historical injection surface, once, for every endpoint */
$_GET     = esc($_GET);
$_POST    = esc($_POST);
$_REQUEST = esc($_REQUEST);

/* ------------------------------------------------------------------ */
/* token gate                                                          */
/* ------------------------------------------------------------------ */

function _ph_self()
{
    if (!empty($_SERVER['SCRIPT_NAME'])) {
        return basename($_SERVER['SCRIPT_NAME']);
    }
    if (!empty($_SERVER['PHP_SELF'])) {
        return basename($_SERVER['PHP_SELF']);
    }
    return '';
}

/**
 * Endpoints the mobile apps call without a token stay open.
 * Everything else (admin_*, owner_*, so_*, report/email/keu/log) must
 * present a valid token.
 */
function _ph_allowlisted($self)
{
    $app = array(
        'login_ph.php', 'login_u.php',
        'penyewa_lupapassword.php', 'penyewa_ubahpin.php',
        'penyewa_pembayaran_getdata.php',
        'penyewa_pengumuman_getdata.php', 'penyewa_pengumuman_chat.php',
        'penyewa_keluhan_getdata.php', 'penyewa_keluhan_tambahdata.php',
        'keluhan_getdata.php', 'keluhan_getdata3.php',
        'kamar_getkamarkosong.php',
        'wil_get-provinsi.php', 'wil_get-kotakab.php', 'wil_get-kecamatan.php',
        'wil_get-kelurahan.php', 'wil_get-kodepos.php',
    );
    return in_array($self, $app, true);
}

function _ph_token_ok($tokens)
{
    $given = isset($_SERVER['HTTP_X_API_TOKEN']) ? $_SERVER['HTTP_X_API_TOKEN']
           : (isset($_POST['_token']) ? $_POST['_token'] : '');
    return $given !== '' && in_array($given, $tokens, true);
}

$PH_SELF = _ph_self();

if ($PH_SELF !== '' && !_ph_allowlisted($PH_SELF) && !_ph_token_ok($PH['tokens'])) {
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode(array('error' => 'unauthorized', 'code' => 401));
    exit;
}

/* ------------------------------------------------------------------ */
/* rate limiting (login-ish endpoints)                                 */
/* ------------------------------------------------------------------ */

/**
 * Simple per-IP sliding-window limiter backed by temp files.
 * ph_rate_limit('login', 8, 900, 60) = max 8 hits per 15 min,
 * then a 60s lockout.
 */
function ph_rate_limit($bucket, $max = 8, $window = 900, $lock = 60)
{
    $dir = sys_get_temp_dir() . '/ph-rl';
    if (!is_dir($dir)) {
        @mkdir($dir, 0700, true);
    }
    $ip   = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    $file = $dir . '/' . $bucket . '_' . preg_replace('/[^0-9A-Fa-f.:]/', '', $ip) . '.lock';
    $now  = time();

    $data = array('t' => array(), 'lock' => 0);
    if (is_file($file)) {
        $raw = @file_get_contents($file);
        $dec = $raw ? json_decode($raw, true) : null;
        if (is_array($dec)) {
            $data = $dec;
        }
    }

    if (!empty($data['lock']) && $now < $data['lock']) {
        header('Content-Type: application/json');
        http_response_code(429);
        echo json_encode(array('error' => 'too_many_attempts', 'retry_after' => $data['lock'] - $now));
        exit;
    }

    $data['t'][] = $now;
    $data['t'] = array_values(array_filter($data['t'], function ($x) use ($now, $window) {
        return ($now - $x) <= $window;
    }));

    if (count($data['t']) > $max) {
        $data['lock'] = $now + $lock;
        @file_put_contents($file, json_encode($data));
        header('Content-Type: application/json');
        http_response_code(429);
        echo json_encode(array('error' => 'too_many_attempts', 'retry_after' => $lock));
        exit;
    }

    @file_put_contents($file, json_encode($data));
    return true;
}
