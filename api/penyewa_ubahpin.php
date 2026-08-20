<?php

include ("kon.php");

$pinlama = $_POST['_pinlama'];
$pinbaru = $_POST['_pinbaru'];
$kode = $_POST['_kode'];

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if( !$koneksi)
{
    return;
}

$querycekpinlama = "SELECT * FROM tb_penyewa WHERE kode = '$kode' AND nomorpin = '$pinlama'";
$resultquerycekpin = mysqli_query($koneksi, $querycekpinlama);

if( mysqli_num_rows($resultquerycekpin) == 1)
{
    $queryubahPIN = "UPDATE tb_penyewa SET nomorpin = '$pinbaru' WHERE kode = '$kode' ";
    $resultubahPIN = mysqli_query($koneksi, $queryubahPIN);
    
    if( $resultubahPIN)
    {
        echo json_encode(array('ubahpin' => "pin berhasil diubah"));
    }
    else
    {
        echo json_encode(array('ubahpin' => "gagal ubah pin"));
    }
}
else
{
    echo json_encode(array('ubahpin' => "tidak ada data"));
}
?>