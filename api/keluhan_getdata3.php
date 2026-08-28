<?php

include "kon.php";

header('Content-Type: application/json; charset=UTF-8');

$userLog = isset($_POST['_kode']) ? trim($_POST['_kode']) : '';
$pin = isset($_POST['_pin']) ? trim($_POST['_pin']) : '';

if (!$koneksi) {
    http_response_code(500);
    echo json_encode(array('getdatakeluhan' => 'koneksi database gagal'));
    return;
}

$akses = '';
$authQuery = '';
if ((substr($userLog, 0, 1) === 'p' || substr($userLog, 0, 1) === 'u')
    && (substr($userLog, 1, 1) === 'a' || substr($userLog, 1, 1) === 'b')) {
    $akses = 'penyewa';
    $authQuery = "SELECT kode FROM tb_penyewa WHERE kode = '$userLog' AND nomorpin = '$pin'";
} elseif (substr($userLog, 0, 1) === 'o' && substr($userLog, 7, 1) === 'w') {
    $akses = 'owner';
    $authQuery = "SELECT kode FROM tb_owner WHERE kode = '$userLog' AND pin = '$pin'";
} elseif (substr($userLog, 0, 1) === 'a' && substr($userLog, 6, 1) === 'd') {
    $akses = 'admin';
    $authQuery = "SELECT kode FROM tb_admin WHERE kode = '$userLog' AND pin = '$pin'";
}

try {
    if ($authQuery === '' || mysqli_num_rows(mysqli_query($koneksi, $authQuery)) !== 1) {
        http_response_code(401);
        echo json_encode(array('getdatakeluhan' => 'tidak ada data member valid'));
        return;
    }

    $where = $akses === 'penyewa' ? "WHERE l.user = '$userLog'" : '';
    $query = "SELECT
                  l.id,
                  l.judul,
                  k.kategori_keluhan AS kategori,
                  l.uraian,
                  l.tgl,
                  l.user,
                  p.nama,
                  p.nohp AS HP,
                  COALESCE((
                      SELECT kamar.nokamar
                      FROM tb_penyewa_kamar pk
                      INNER JOIN tb_sewa_kamar sewa ON sewa.kode_sewa = pk.kode_sewa
                      INNER JOIN tb_kamar kamar ON kamar.id = sewa.kode_kamar
                      WHERE pk.kode_penyewa = p.kode
                        AND sewa.tanggal_selesai IS NULL
                      ORDER BY sewa.kode_sewa DESC
                      LIMIT 1
                  ), '') AS nokamar
              FROM tb_keluhan_list l
              INNER JOIN tb_keluhan_kategori k ON k.kode_keluhan = l.kategori
              INNER JOIN tb_penyewa p ON p.kode = l.user
              $where
              ORDER BY l.tgl DESC";

    $result = mysqli_query($koneksi, $query);
    $complaints = array();

    $chatHasComplaintId = mysqli_num_rows(
        mysqli_query($koneksi, "SHOW COLUMNS FROM tb_keluhan_chat LIKE 'kode_keluhan'")
    ) > 0;

    while ($row = mysqli_fetch_assoc($result)) {
        $id = (int) $row['id'];
        $statuses = array();
        $statusResult = mysqli_query(
            $koneksi,
            "SELECT kode, status, tglupdate FROM tb_keluhan_updatetgl
             WHERE kode = $id ORDER BY tglupdate ASC, id ASC"
        );
        while ($status = mysqli_fetch_assoc($statusResult)) {
            $statuses[] = array(
                'id' => $status['kode'],
                'status' => $status['status'],
                'tgl' => $status['tglupdate'],
            );
        }

        $photos = array();
        $photoResult = mysqli_query(
            $koneksi,
            "SELECT id, urlfoto FROM tb_keluhan_foto WHERE id = $id"
        );
        while ($photo = mysqli_fetch_assoc($photoResult)) {
            $photos[] = array('id' => $photo['id'], 'link' => $photo['urlfoto']);
        }

        $chats = array();
        if ($chatHasComplaintId) {
            $chatResult = mysqli_query(
                $koneksi,
                "SELECT idchatkeluhan, kode_keluhan, kode_user, key_user, date_time, message
                 FROM tb_keluhan_chat WHERE kode_keluhan = $id ORDER BY date_time ASC"
            );
            while ($chat = mysqli_fetch_assoc($chatResult)) {
                $chats[] = array(
                    'kodchat' => $chat['idchatkeluhan'],
                    'kodkeluhan' => $chat['kode_keluhan'],
                    'koduser' => $chat['kode_user'],
                    'keyuser' => $chat['key_user'],
                    'dttime' => $chat['date_time'],
                    'msg' => $chat['message'],
                );
            }
        }

        $complaints[] = array(
            'kode' => $row['id'],
            'judul' => $row['judul'],
            'kategori' => $row['kategori'],
            'uraian' => $row['uraian'],
            'tgl' => $row['tgl'],
            'user' => $row['user'],
            'nokamar' => $row['nokamar'],
            'nama' => $row['nama'],
            'hp' => $row['HP'],
            'chat' => $chats,
            'foto' => $photos,
            'status' => $statuses,
        );
    }

    echo json_encode(array(
        'keluhan' => $complaints ? $complaints : 'tidak ada data keluhan',
    ));
} catch (Throwable $e) {
    error_log('keluhan_getdata3 failed: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(array('getdatakeluhan' => 'gagal mengambil data keluhan'));
}
