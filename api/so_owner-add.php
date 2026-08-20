<?php

include ("kon.php");

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if( !$koneksi)
{
    return;
}

$admin = array();

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

echo json_encode(array('kode' => $kode));

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