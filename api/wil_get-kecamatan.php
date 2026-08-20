<?php

include ("kon.php");

$prov = $_POST['_provinsi'];
$kotakab = $_POST['_kotakab'];

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if( !$koneksi)
{
    return;
}

$datakecamatan = array();
$hasil;

// get data kecamatan -----------------------------------------
$querygetkecamatan = "SELECT DISTINCT kecamatan FROM tb_master_prov WHERE provinsi = '$prov' AND kotakab = '$kotakab'";
$resultgetkecamatan = mysqli_query($koneksi, $querygetkecamatan);

if( mysqli_num_rows($resultgetkecamatan) > 0)
{
    while($rows = mysqli_fetch_assoc($resultgetkecamatan))
    {
        array_push($datakecamatan, array('kecamatan' => $rows['kecamatan']));
    }
    $hasil = json_encode(array('getdatakecamatan' => $datakecamatan));
}
else
{
    $datakecamatan = null;
    $hasil = json_encode(array('getdatakecamatan' => 'tidak ada data kecamatan'));
}

echo $hasil;
?>