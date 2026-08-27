<?php

include ("kon.php");

$bulan = $_POST['_bulan'];
$tahun = $_POST['_tahun'];

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if (!$koneksi)
{
    header('Content-Type: application/json');
    echo json_encode(array('kas' => 'koneksi database gagal'));
    return;
}

$querygetdebit = "SELECT tb_keu_jurnal_umum.kode_akun, nama_akun, SUM(jumlah) AS saldo
                   FROM `tb_keu_jurnal_umum`
                   INNER JOIN tb_keu_akun ON tb_keu_jurnal_umum.kode_akun=tb_keu_akun.kode_akun
                   WHERE tanggal >= '2018-01-01' AND tanggal <= CONCAT($tahun, '-', $bulan, '-', '31')
                   AND posisi = 'Debit'
                   GROUP BY tb_keu_jurnal_umum.kode_akun
                   ORDER BY tb_keu_jurnal_umum.kode_akun";

$resultgetdebit = mysqli_query($koneksi, $querygetdebit);

if(mysqli_num_rows($resultgetdebit) > 0)
{
	while($rows = mysqli_fetch_assoc($resultgetdebit))
    {
        $posisikeu[] = array(
            "nama_akun"  => $rows['nama_akun'],
            "saldo"		 => $rows['saldo']

        );
    }
}
else
{
	$posisikeu = null;
}

$querygetkredit = "SELECT tb_keu_jurnal_umum.kode_akun, nama_akun, SUM(jumlah) AS saldo
                   FROM `tb_keu_jurnal_umum`
                   INNER JOIN tb_keu_akun ON tb_keu_jurnal_umum.kode_akun=tb_keu_akun.kode_akun
                   WHERE tanggal >= '2018-01-01' AND tanggal <= CONCAT($tahun, '-', $bulan, '-', '31')
                   AND posisi = 'Kredit'
                   GROUP BY tb_keu_jurnal_umum.kode_akun
                   ORDER BY tb_keu_jurnal_umum.kode_akun";

$resultgetkredit = mysqli_query($koneksi, $querygetkredit);

if(mysqli_num_rows($resultgetkredit) > 0)
{
	while($rows = mysqli_fetch_assoc($resultgetkredit))
    {
        $posisikeu[] = array(
            "nama_akun"  => $rows['nama_akun'],
            "saldo"		 => $rows['saldo']

        );
    }
}
else
{
	$posisikeu = null;
}

echo json_encode(array(
    'kas'   => 200000,
    'bank'  => 100000,));

?>