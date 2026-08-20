<?php

include ("kon.php");

$koneksi = mysqli_connect($host, $user, $pass, $daba);

$kodekost = $_POST['_kodekost'];

if( !$koneksi)
{
    return;
}

$datapenyewa = array();

// get data semua penyewa -----------------------------------------
$querygetallpenyewa = "SELECT tb_penyewa.kode, tb_penyewa.nama, 
                       tb_penyewa.status, penyewa_aktif.nokamar
                       
                       FROM tb_penyewa       
                       
                       INNER JOIN (
                            
                            SELECT kode, nama, status, tb_kamar.nokamar
                            
						    FROM tb_penyewa
						    
						    INNER JOIN tb_penyewa_kamar
						    ON tb_penyewa.kode=tb_penyewa_kamar.kode_penyewa
						    
						    INNER JOIN tb_sewa_kamar
						    ON tb_penyewa_kamar.kode_sewa=tb_sewa_kamar.kode_sewa
						    
						    INNER JOIN tb_kamar
						    ON tb_sewa_kamar.kode_kamar=tb_kamar.id
						    
						    WHERE tb_penyewa.kode_kost='$kodekost'
						    AND tb_sewa_kamar.tanggal_selesai IS NULL
						    
					   ) AS penyewa_aktif
					   
					   ON penyewa_aktif.kode=tb_penyewa.kode
					   
					   WHERE tb_penyewa.kode_kost='$kodekost'
					   
					   GROUP BY kode";
					   
$resultgetallpenyewa = mysqli_query($koneksi, $querygetallpenyewa);
if( mysqli_num_rows($resultgetallpenyewa) > 0)
{
    while($rows = mysqli_fetch_assoc($resultgetallpenyewa))
    {
        $datapenyewa[] = array(
            
            "kode" => $rows['kode'],
            "nama" => $rows['nama'],
            "status" => $rows['status'],
            "nokamar" => $rows['nokamar']
        );
    }
    
}
else
{
    $datapenyewa = null;
}

echo json_encode(array('datapenyewa' => $datapenyewa));

?>