<?php

include ("kon.php");

$prov = $_POST['_provinsi'];
$kotakab = $_POST['_kotakab'];
$kec = $_POST['_kecamatan'];

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if (!$koneksi)
{
    header('Content-Type: application/json');
    echo json_encode(array('error' => 'koneksi database gagal'));
    return;
}

$datakelurahan = array();
$hasil;

// get data kecamatan -----------------------------------------
$querygetkelurahan = "SELECT * FROM tb_master_prov WHERE provinsi = '$prov' AND kotakab = '$kotakab' AND kecamatan = '$kec'";
$resultgetkelurahan = mysqli_query($koneksi, $querygetkelurahan);

if( mysqli_num_rows($resultgetkelurahan) > 0)
{
    while($rows = mysqli_fetch_assoc($resultgetkelurahan))
    {
        array_push($datakelurahan, array('kelurahan' => $rows['kelurahan']));
    }
    $hasil = json_encode(array('getdatakelurahan' => $datakelurahan));
}
else
{
    $datakecamatan = null;
    $hasil = json_encode(array('getdatakelurahan' => 'tidak ada data kelurahan'));
}

echo $hasil;
?>