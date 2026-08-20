<?php

include ("kon.php");


$koneksi = mysqli_connect($host, $user, $pass, $daba);

if( !$koneksi)
{
    return;
}

$dataprovinsi = array();
$hasil;

// get all province -----------------------------------------
$querygetprovince = "SELECT DISTINCT provinsi FROM tb_master_prov";
$resultprovince = mysqli_query($koneksi, $querygetprovince);

if( mysqli_num_rows($resultprovince) > 0)
{
    while($rows = mysqli_fetch_assoc($resultprovince))
    {
        array_push($dataprovinsi, array('provinsi' => $rows['provinsi']));
    }
}
else
{
    $dataprovinsi = null;
}

$hasil = json_encode(array('getdataprov' => $dataprovinsi));
echo $hasil;

?>