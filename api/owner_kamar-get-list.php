<?php

include ("kon.php");

$koneksi = mysqli_connect($host, $user, $pass, $daba);

$kodekost = $_POST['_kodekost'];

if( !$koneksi)
{
    return;
}

$datakamar = array();

// get data semua kamar -----------------------------------------
$querygetallkamar = "SELECT id, nokamar, statuskamar,
                    harga, sewa_kamar.harga_perbulan
                    FROM tb_kamar
                    LEFT JOIN (
                    SELECT * FROM tb_sewa_kamar
                    WHERE tanggal_selesai IS NULL
                    ) sewa_kamar ON sewa_kamar.kode_kamar=tb_kamar.id
                    WHERE tb_kamar.kode_kost='$kodekost'
                    ORDER BY CAST(nokamar AS INTEGER) ASC";
                    
$resultgetallkamar = mysqli_query($koneksi, $querygetallkamar);
if( mysqli_num_rows($resultgetallkamar) > 0)
{
    while($rows = mysqli_fetch_assoc($resultgetallkamar))
    {
        $datakamar[] = array(
            
            "kode"           => $rows['id'],
            "nokamar"        => $rows['nokamar'],
            "statuskamar"    => $rows['statuskamar'],
            "harga"          => $rows['harga'],
            "hargadisewakan" => $rows['harga_perbulan']
        );
    }
    
}
else
{
    $datakamar = null;
}

echo json_encode(array('datakamar' => $datakamar));

?>