<?php

include ("kon.php");

$kode = $_POST['_kodekamar'];

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if( !$koneksi)
{
    return;
}

// get data semua penyewa -----------------------------------------
$querygethargakamar = "SELECT IF(sewa_kamar.harga_perbulan <> '', sewa_kamar.harga_perbulan, tb_kamar.harga) AS harga_kamar,
COUNT(tb_penyewa_kamar.kode_penyewa) AS jumlah_penyewa,
tb_kamar.nokamar
                FROM tb_kamar
                LEFT JOIN (SELECT * FROM tb_sewa_kamar WHERE tanggal_selesai IS NULL) sewa_kamar ON tb_kamar.id=sewa_kamar.kode_kamar
                LEFT JOIN tb_penyewa_kamar ON tb_penyewa_kamar.kode_sewa=sewa_kamar.kode_sewa
                WHERE tb_kamar.id='$kode'";
                
$resultgethargakamar = mysqli_query($koneksi, $querygethargakamar);
if( mysqli_num_rows($resultgethargakamar) > 0)
{
    while($rows = mysqli_fetch_assoc($resultgethargakamar))
    {
        $hargakamar = $rows['harga_kamar'];
        $jumlahpenyewa = $rows['jumlah_penyewa'];
        $nokamar = $rows['nokamar'];
    }
    
}
else
{
    $kamar = null;
}

echo json_encode(array("hargakamar" => $hargakamar, "jumlahpenyewa" => $jumlahpenyewa, "nokamar" => $nokamar));

?>