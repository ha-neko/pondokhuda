<?php

include ("kon.php");

//$kode = $_POST['_kode'];
$nama = $_POST['_nama'];
$pin = GetRandomPIN();
$notelp = $_POST['_notelp'];
$foto = $_POST['_foto'];
$email = $_POST['_email'];

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if( !$koneksi)
{
    return;
}

// get data semua kamar -----------------------------------------
$querygetkode = "SELECT kode FROM tb_owner ORDER BY kode DESC LIMIT 0,1";
                    
$resultgetkode = mysqli_query($koneksi, $querygetkode);
if( mysqli_num_rows($resultgetkode) > 0)
{
    while($rows = mysqli_fetch_assoc($resultgetkode))
    {
        $kode = GetNextKode($rows['kode']);
    }
}
else
{
    $kode = "o000w";
}

$IMAGE_FOLDER = "/Assets/images/owner/";

$isUpload = false;
$owner = 'gagal tambah owner';

if($foto != "0")
{
    $decoded = base64_decode($foto, true);
    $imageInfo = $decoded !== false && function_exists('getimagesizefromstring')
        ? @getimagesizefromstring($decoded) : false;
    $extensions = array(
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
    );
    $mime = $imageInfo && isset($imageInfo['mime']) ? $imageInfo['mime'] : '';
    if ($decoded === false || strlen($decoded) > 921600 || !isset($extensions[$mime])) {
        $owner = "foto tidak valid atau melebihi 900 KB";
    } else {
        try {
            $ownerDir = ph_public_asset_root() . '/images/owner';
            if (!is_dir($ownerDir)) {
                @mkdir($ownerDir, 0755, true);
            }
            $extension = $extensions[$mime];
            $target = $ownerDir . '/' . $kode . '.' . $extension;
            if (is_dir($ownerDir) && is_writable($ownerDir) && file_put_contents($target, $decoded, LOCK_EX) !== false) {
                $isUpload = true;
                $urlfoto = $IMAGE_FOLDER . $kode . '.' . $extension;
            } else {
                $owner = "upload foto gagal: direktori tidak dapat ditulis";
                error_log('pondokhuda owner photo directory is not writable');
            }
        } catch (RuntimeException $ex) {
            $owner = "upload foto gagal: direktori tidak tersedia";
            error_log('pondokhuda owner photo upload failed: ' . $ex->getMessage());
        }
    }
}
else
{
    $isUpload = true;
    $urlfoto = $IMAGE_FOLDER . 'noimage' . ".png";
}
$kodeOwner = "";
$tgldaftar = date('Y-m-d');
$isOwner = false;
if($isUpload)
{
    $queryinsertowner = "INSERT INTO tb_owner
                        (kode, nama, pin, nomor_telepon, email, tanggal_terdaftar, urlfoto)
                        VALUES ('$kode', '$nama', '$pin', '$notelp', '$email',  '$tgldaftar', '$urlfoto')";
                
    $resultinsertowner = mysqli_query($koneksi, $queryinsertowner);
    
    if($resultinsertowner)
    {
        $isOwner = true;
        $kodeOwner = $kode;                
    }
    else
    {
        $owner = 'gagal tambah owner, Error: '. mysqli_error($koneksi);
    }
}

//---------------------- KIRIM EMAIL ----------------------\\
if($isOwner)
{
    $querygetowner = "SELECT kode, nama, email, pin FROM tb_owner WHERE kode='$kodeOwner'";
        $resultowner = mysqli_query($koneksi, $querygetowner);
     
    $_kode;
    $_nama;
    $_email;
    $_nomorpin;
    
    if( mysqli_num_rows($resultowner) > 0)
    {
        while($rows = mysqli_fetch_assoc($resultowner))
        {
            $_kode = $rows['kode'];
            $_nama = $rows['nama'];
            $_email = $rows['email'];
            $_nomorpin = $rows['pin'];
        }
        // echo "\nkode: " . $_kode;
        // echo "\nnama: " . $_nama;
        // echo "\nemail: " . $_email;
        // echo "\npin: " . $_nomorpin;
        
        $sent = ph_send_mail($_email, "Pendaftaran Owner Baru", "Selamat anda telah teregistrasi dalam sistem informasi PONDOK HUDA, atas nama " . $_nama . " :-) <br><br>Kode Akses: <b>" . $_kode . "</b><br>Nomor PIN: <b>" . $_nomorpin . "</b><br><br>Silahkan login dengan kode akses dan PIN tersebut pada <a href='https://admin-pondokhuda.jannahku.com/'>Aplikasi Si Juragan Kost</a>. TERIMA KASIH :) <br><<<NO-EMAIL-REPLY>>>", ph_html_mail_headers());
        $owner = $sent ? 'owner berhasil ditambah' : 'owner berhasil ditambah, email gagal dikirim';
    }
}

// ------------------------END SEND EMAIL-----------------------------

echo json_encode(array('owner' => $owner));

function GetNextKode($kode)
{
    $noUrutTerakhir = substr($kode, 1, 3);
    
    $noUrutBaru =  $noUrutTerakhir + 1;
    
    $hasil = 'o'.substr($noUrutTerakhir, 0, -1 * abs(strlen($noUrutBaru))).$noUrutBaru.'w';
    
    return $hasil;
}
function GetRandomPIN()
{
    $pin = rand(0,9);
    
    for($i = 0; $i < 5; $i++)
    {
        $pin .= rand(0,9);
    }
    return $pin;
}

?>
