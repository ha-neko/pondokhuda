<?php

include ("kon.php");

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if (!$koneksi)
{
    header('Content-Type: application/json');
    echo json_encode(array('jenispengeluaran' => 'koneksi database gagal'));
    return;
}

$hasil;

// get data kamar -----------------------------------------
$querygetjenispeng = "SELECT kode_akun, nama_akun
				  FROM tb_keu_akun
				  WHERE kode_jenis_akun=4";
				
$resultgetjenispeng = mysqli_query($koneksi, $querygetjenispeng);

if( mysqli_num_rows($resultgetjenispeng) > 0)
{
    while($rows = mysqli_fetch_assoc($resultgetjenispeng))
    {
        $jenispengeluaran[] = array(
            
            "kodeakun" => $rows['kode_akun'],
            "namaakun" => $rows['nama_akun']
            
        );
    }
    
}
else
{
    $jenispengeluaran = null;
}

echo json_encode(array('jenispengeluaran' => $jenispengeluaran));

?>