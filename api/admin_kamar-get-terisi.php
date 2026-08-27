<?php

include ("kon.php");

$koneksi = mysqli_connect($host, $user, $pass, $daba);

$kodekost = $_POST['_kodekost'];

if (!$koneksi)
{
    header('Content-Type: application/json');
    echo json_encode(array('kamar' => 'koneksi database gagal'));
    return;
}

$hasil;

// get data kamar -----------------------------------------
$querygetkamar = "SELECT kode_kamar, nokamar
				FROM tb_sewa_kamar
				INNER JOIN tb_kamar ON tb_sewa_kamar.kode_kamar = tb_kamar.id
				WHERE tb_sewa_kamar.tanggal_selesai IS NULL
				AND tb_kamar.statuskamar='Untuk Sewa'
				AND tb_kamar.kode_kost='$kodekost'
				ORDER BY CAST(nokamar AS INTEGER) ASC";
				
$resultgetkamar = mysqli_query($koneksi, $querygetkamar);

if( mysqli_num_rows($resultgetkamar) > 0)
{
    while($rows = mysqli_fetch_assoc($resultgetkamar))
    {
        $kamar[] = array(
            
            "kode_kamar" => $rows['kode_kamar'],
            "nokamar"	 => $rows['nokamar']
            
        );
    }
    
}
else
{
    $kamar = null;
}

echo json_encode(array('kamar' => $kamar));

?>