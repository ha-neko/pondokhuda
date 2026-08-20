<?php

include ("kon.php");
include("owner_log.php");

$nokamar = $_POST['_nokamar'];
$harga = $_POST['_hargakamar'];
$kodeowner = $_POST['_kodeowner'];
$namaowner = $_POST['_namaowner'];
$kodekost = $_POST['_kodekost'];

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if( !$koneksi)
{
    return;
}

$datakamar = array();
$tgldibuat = date('Y-m-d');
// get data semua penyewa -----------------------------------------
$queryinsertkamar = "INSERT INTO tb_kamar(nokamar, statuskamar, harga, tanggal_dibuat, kode_kost) VALUES('$nokamar', 'Untuk Sewa', '$harga', '$tgldibuat', '$kodekost')";
$resultinsertkamar = mysqli_query($koneksi, $queryinsertkamar);
if($resultinsertkamar)
{
    $log = CreateLogOwner($koneksi, $namaowner.' menambahkan kamar dengan nomor kamar '.$nokamar, $kodeowner, $kodekost);
    echo json_encode(array('datakamar' => 'data berhasil ditambah'));
}
else
{
    $log = CreateLogOwner($koneksi, $namaowner.' menambahkan kamar dengan nomor kamar '.$nokamar.' Error: '.mysqli_error($koneksi), $kodeowner, $kodekost);
    echo json_encode(array('datakamar' => 'data gagal ditambah, Error: '. mysqli_error($koneksi)));
}

?>