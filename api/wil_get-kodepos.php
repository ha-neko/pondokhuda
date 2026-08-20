<?php

include ("kon.php");

$provinsi = $_POST['_provinsi'];
$kotakab = $_POST['_kotakab'];
$kelurahan = $_POST['_kelurahan'];
$kecamatan = $_POST['_kecamatan'];

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if( !$koneksi)
{
    return;
}

$kodepos = array();
$hasil;

// get data kecamatan -----------------------------------------
$querykodepos = "SELECT * FROM tb_master_prov WHERE  kelurahan = '$kelurahan' AND provinsi = '$provinsi' AND kecamatan = '$kecamatan'";
$resultkodepos = mysqli_query($koneksi, $querykodepos);

if( mysqli_num_rows($resultkodepos) > 0)
{
    while($rows = mysqli_fetch_assoc($resultkodepos))
    {
        array_push($kodepos, array('kodepos' => $rows['kodepos']));
    }
    $hasil = json_encode(array('getdatakodepos' => $kodepos));
}
else
{
    $kodepos = null;
    $hasil = json_encode(array('getdatakodepos' => 'tidak ada data kecamatan'));
}

echo $hasil;
?>