<?php

include ("kon.php");

$kodekamar = $_POST['_kodekamar'];
$kodekost = $_POST['_kodekost'];

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if (!$koneksi)
{
    header('Content-Type: application/json');
    echo json_encode(array('datakamar' => 'koneksi database gagal'));
    return;
}

$datakamar = array();

// get data semua penyewa -----------------------------------------
$querygetdetailkamar = "SELECT tb_kamar.id, nokamar, statuskamar,
                  harga, tb_sewa_kamar.harga_perbulan,
                  COUNT(kode_penyewa) AS jml_penyewa
                  
                  FROM tb_kamar
                  
                  LEFT JOIN (
                             SELECT * FROM tb_sewa_kamar
                             WHERE tanggal_selesai IS NULL
                  ) tb_sewa_kamar
                  ON tb_kamar.id=tb_sewa_kamar.kode_kamar
                  
                  LEFT JOIN tb_penyewa_kamar
                  ON tb_sewa_kamar.kode_sewa=tb_penyewa_kamar.kode_sewa
                  
                  WHERE tb_kamar.id='$kodekamar'
                  AND tb_kamar.kode_kost='$kodekost'";
                  
$resultgetdetailkamar = mysqli_query($koneksi, $querygetdetailkamar);
if( mysqli_num_rows($resultgetdetailkamar) > 0)
{
    while($rows = mysqli_fetch_assoc($resultgetdetailkamar))
    {
        $datakamar[] = array(
            "kode"           => $rows['id'],
            "nokamar"        => $rows['nokamar'],
            "statuskamar"    => $rows['statuskamar'],
            "harga"          => $rows['harga'],
            "hargadisewakan" => $rows['harga_perbulan'],
            "jmlpenyewa"     => $rows['jml_penyewa']
        );
    }
    
}
else
{
    $datakamar = null;
}

echo json_encode(array('datakamar' => $datakamar));

?>