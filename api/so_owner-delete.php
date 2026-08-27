<?php

include ("kon.php");

$kode = $_POST['_kode'];

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if (!$koneksi)
{
    header('Content-Type: application/json');
    echo json_encode(array('error' => 'koneksi database gagal'));
    return;
}

$queryupdateowner = "DELETE FROM tb_owner WHERE kode='$kode'";
                
$resultupdateowner = mysqli_query($koneksi, $queryupdateowner);

if($resultupdateowner)
{
    $isUpdate = true;
    $owner = array('owner' => "owner berhasil dihapus");
}
else
{
    $owner = array('owner' => 'gagal hapus data owner, Error: '. mysqli_error($koneksi));
}

echo json_encode($owner);

?>