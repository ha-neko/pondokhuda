<?php

include ("kon.php");

$kode = $_POST['_kodekeluhan'];

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if (!$koneksi)
{
    header('Content-Type: application/json');
    echo json_encode(array('komen' => 'koneksi database gagal'));
    return;
}

// get data semua penyewa -----------------------------------------
$querygetdetailkamar = "SELECT komen, created
                  FROM tb_keluhan_komenadmin
                  WHERE kode_kel='$kode' ORDER BY tb_keluhan_komenadmin.created DESC";
                  
$resultgetdetailkamar = mysqli_query($koneksi, $querygetdetailkamar);
if( mysqli_num_rows($resultgetdetailkamar) > 0)
{
    while($rows = mysqli_fetch_assoc($resultgetdetailkamar))
    {
        $komen[] = array(
            "komen"           => $rows['komen'],
            "created"        => $rows['created']
        );
    }
    
}
else
{
    $komen = null;
}

echo json_encode(array('komen' => $komen));

?>