<?php

include ("kon.php");

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if (!$koneksi)
{
    header('Content-Type: application/json');
    echo json_encode(array('kode' => 'koneksi database gagal'));
    return;
}

$admin = array();

// get data semua kamar -----------------------------------------
$querygetkode = "SELECT kode FROM tb_admin ORDER BY kode DESC LIMIT 0,1";
                    
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
    $kode = "a00000d";
}

echo json_encode(array('kode' => $kode));

function GetNextKode($kode)
{
    $noUrutTerakhir = substr($kode, 1, 5);
    
    $noUrutBaru =  $noUrutTerakhir + 1;
    
    $hasil = 'a'.substr($noUrutTerakhir, 0, -1 * abs(strlen($noUrutBaru))).$noUrutBaru.'d';
    
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