<?php

include ("kon.php");

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if( !$koneksi)
{
    return;
}

$hasil;

// get data kamar -----------------------------------------
$querygetkamar = "SELECT id, nokamar
				  FROM tb_kamar
				  WHERE tb_kamar.statuskamar='Untuk Sewa' AND id NOT IN (
				  SELECT kode_kamar
				  FROM tb_sewa_kamar
				  INNER JOIN tb_kamar ON tb_sewa_kamar.kode_kamar = tb_kamar.id
				  WHERE tb_sewa_kamar.tanggal_selesai IS NULL AND tb_kamar.statuskamar='Untuk Sewa')";
				
$resultgetkamar = mysqli_query($koneksi, $querygetkamar);

if( mysqli_num_rows($resultgetkamar) > 0)
{
    while($rows = mysqli_fetch_assoc($resultgetkamar))
    {
        $kamar[] = array(
            
            "kode_kamar" => $rows['id'],
            "nokamar"	 => $rows['nokamar']
            
        );
    }
    
}
else
{
    $kamar = null;
}

echo json_encode(array('datakamar' => $kamar));

?>