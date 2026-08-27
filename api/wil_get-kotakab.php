<?php

include ("kon.php");

$prov = $_POST['_provinsi'];

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if (!$koneksi)
{
    header('Content-Type: application/json');
    echo json_encode(array('error' => 'koneksi database gagal'));
    return;
}

$datakotakab = array();
$hasil;

// get data kecamatan -----------------------------------------
$querygetkotakab = "SELECT DISTINCT kotakab FROM tb_master_prov WHERE provinsi = '$prov'";
$resultgetkotakab = mysqli_query($koneksi, $querygetkotakab);

if( mysqli_num_rows($resultgetkotakab) > 0)
{
    while($rows = mysqli_fetch_assoc($resultgetkotakab))
    {
        array_push($datakotakab, array('kotakab' => $rows['kotakab']));
    }
}
else
{
    $datakotakab = null;
}

$hasil = json_encode(array('getdatakotakab' => $datakotakab));

echo $hasil;
?>