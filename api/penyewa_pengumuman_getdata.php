<?php

include "kon.php";

header('Content-Type: application/json; charset=UTF-8');

$userLog = isset($_POST['_kode']) ? trim($_POST['_kode']) : '';

if (!$koneksi) {
    http_response_code(500);
    echo json_encode(array('pengumuman' => 'koneksi database gagal'));
    return;
}

if ($userLog === '') {
    http_response_code(400);
    echo json_encode(array('pengumuman' => 'kode pengguna kosong'));
    return;
}

$akses = '';
if ((substr($userLog, 0, 1) === 'p' || substr($userLog, 0, 1) === 'u')
    && (substr($userLog, 1, 1) === 'a' || substr($userLog, 1, 1) === 'b')) {
    $akses = 'penyewa';
} elseif (substr($userLog, 0, 1) === 'o' && substr($userLog, 7, 1) === 'w') {
    $akses = 'owner';
} elseif (substr($userLog, 0, 1) === 'a' && substr($userLog, 6, 1) === 'd') {
    $akses = 'admin';
}

if ($akses === '') {
    http_response_code(400);
    echo json_encode(array('pengumuman' => 'kode pengguna tidak valid'));
    return;
}

try {
    $chatByAnnouncement = array();
    $chatQuery = "SELECT kode_chat, kode_berita, kode_user, key_user, date_time, message
                  FROM tb_beritakost_chat
                  WHERE kode_user = '$userLog'
                  ORDER BY date_time ASC";
    $chatResult = mysqli_query($koneksi, $chatQuery);
    while ($chat = mysqli_fetch_assoc($chatResult)) {
        $announcementId = (string) $chat['kode_berita'];
        if (!isset($chatByAnnouncement[$announcementId])) {
            $chatByAnnouncement[$announcementId] = array();
        }
        $chatByAnnouncement[$announcementId][] = array(
            'kodchat' => $chat['kode_chat'],
            'kodberita' => $chat['kode_berita'],
            'koduser' => $chat['kode_user'],
            'keyuser' => $chat['key_user'],
            'dttime' => $chat['date_time'],
            'msg' => $chat['message'],
        );
    }

    if ($akses === 'penyewa') {
        $query = "SELECT kode, judul, berita, tglpublish, lastupdate
                  FROM tb_beritakost
                  WHERE status = 'tampil'
                    AND kode_kost = (
                        SELECT kode_kost FROM tb_penyewa WHERE kode = '$userLog' LIMIT 1
                    )
                  ORDER BY tglpublish DESC";
    } else {
        $query = "SELECT kode, judul, berita, tglpublish, lastupdate
                  FROM tb_beritakost
                  WHERE status = 'tampil'
                  ORDER BY tglpublish DESC";
    }

    $result = mysqli_query($koneksi, $query);
    $announcements = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $id = (string) $row['kode'];
        $announcements[] = array(
            'kode' => $row['kode'],
            'judul' => $row['judul'],
            'berita' => $row['berita'],
            'tglpublish' => $row['tglpublish'],
            'lastupdate' => $row['lastupdate'],
            'chat' => isset($chatByAnnouncement[$id]) ? $chatByAnnouncement[$id] : array(),
        );
    }

    echo json_encode(array(
        'pengumuman' => $announcements ? $announcements : 'tidak ada data pengumuman',
    ));
} catch (Throwable $e) {
    error_log('penyewa_pengumuman_getdata failed: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(array('pengumuman' => 'gagal mengambil data pengumuman'));
}
