<?php

include ("kon.php");
include("owner_log.php");

$kode = $_POST['_kode'];
$kodeEdit = $_POST['_kode-edit'];
$nama = $_POST['_nama'];
$notelp = $_POST['_notelp'];
$email = $_POST['_email'];

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if (!$koneksi)
{
    header('Content-Type: application/json');
    echo json_encode(array('error' => 'koneksi database gagal'));
    return;
}

$queryupdateowner = "UPDATE tb_owner SET kode='$kodeEdit', nama='$nama',
                     nomor_telepon='$notelp', email='$email'
                     WHERE kode='$kode'";
                
$resultupdateowner = mysqli_query($koneksi, $queryupdateowner);

if($resultupdateowner)
{
    $isUpdate = true;
    $owner = array('owner' => "owner berhasil diubah");
}
else
{
    $owner = array('owner' => 'gagal ubah data owner, Error: '. mysqli_error($koneksi));
}

echo json_encode($owner);

?>