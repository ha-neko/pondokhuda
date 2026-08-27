<?php

include ("kon.php");

$bulan = $_POST['_bulan'];
$tahun = $_POST['_tahun'];
$kodekost = $_POST['_kodekost'];

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if (!$koneksi)
{
    header('Content-Type: application/json');
    echo json_encode(array('error' => 'koneksi database gagal'));
    return;
}

/*$querygetjurnal = "SELECT kode_transaksi, DATE_FORMAT(tanggal,'%d/%m/%Y') AS tanggal, keterangan, posisi, jumlah
				   FROM `tb_keu_jurnal_umum`
				   WHERE MONTH(tanggal)=$bulan AND YEAR(tanggal)=$tahun
				   ORDER BY tanggal ASC, kode_transaksi ASC, FIELD(posisi, 'Debit','Kredit') ASC";

$resultgetjurnal = mysqli_query($koneksi, $querygetjurnal);

if(mysqli_num_rows($resultgetjurnal) > 0)
{
	while($rows = mysqli_fetch_assoc($resultgetjurnal))
    {
        $jurnal[] = array(
            "kode_transaksi" => $rows['kode_transaksi'],
            "tanggal"		 => $rows['tanggal'],
            "keterangan"	 => $rows['keterangan'],
            "posisi"		 => $rows['posisi'],
            "jumlah"		 => $rows['jumlah']

        );
    }
}
else
{
	$jurnal = null;
}*/

$querygetpendapatan = "SELECT DATE_FORMAT(tanggal_bayar,'%d/%m/%Y') AS tanggal,
                   CONCAT('Pendapatan Sewa Kost Kamar ', tb_kamar.nokamar) AS keterangan,
                   metode, total_bayar
                   FROM `tb_bayar_kost`
                   INNER JOIN tb_sewa_kamar ON tb_bayar_kost.kode_sewa=tb_sewa_kamar.kode_sewa
                   INNER JOIN tb_kamar ON tb_sewa_kamar.kode_kamar=tb_kamar.id
                   WHERE MONTH(tanggal_bayar)='$bulan' AND YEAR(tanggal_bayar)='$tahun' AND kode_kost='$kodekost'
                   ORDER BY tanggal_bayar ASC";

$resultgetpendapatan = mysqli_query($koneksi, $querygetpendapatan);

if(mysqli_num_rows($resultgetpendapatan) > 0)
{
	while($rows = mysqli_fetch_assoc($resultgetpendapatan))
    {
        $pendapatan[] = array(
            "tanggal"	 => $rows['tanggal'],
            "keterangan" => $rows['keterangan'],
            "metode"	 => $rows['metode'],
            "jumlah"	 => $rows['total_bayar']

        );
    }
}
else
{
	$pendapatan = null;
}

$querygetpendlain = "SELECT DATE_FORMAT(tanggal,'%d/%m/%Y') AS tanggal,
                        keterangan, metode, nominal
                        FROM tb_pendapatan_lainnya
                        WHERE MONTH(tanggal)=$bulan
                        AND YEAR(tanggal)=$tahun
                        AND kode_kost='$kodekost'
                        ORDER BY tanggal ASC";

$resultgetpendlain = mysqli_query($koneksi, $querygetpendlain);

if(mysqli_num_rows($resultgetpendlain) > 0)
{
	while($rows = mysqli_fetch_assoc($resultgetpendlain))
    {
        $pendapatan[] = array(
            "tanggal"	 => $rows['tanggal'],
            "keterangan" => $rows['keterangan'],
            "metode"	 => $rows['metode'],
            "jumlah"	 => $rows['nominal']

        );
    }
}

$querygetpengeluaran = "SELECT DATE_FORMAT(tanggal,'%d/%m/%Y') AS tanggal,
                        keterangan, metode, nominal
                        FROM tb_pengeluaran
                        WHERE MONTH(tanggal)=$bulan
                        AND YEAR(tanggal)=$tahun
                        AND kode_kost='$kodekost'
                        ORDER BY tanggal ASC";

$resultgetpengeluaran = mysqli_query($koneksi, $querygetpengeluaran);

if(mysqli_num_rows($resultgetpengeluaran) > 0)
{
	while($rows = mysqli_fetch_assoc($resultgetpengeluaran))
    {
        $pengeluaran[] = array(
            "tanggal"	 => $rows['tanggal'],
            "keterangan" => $rows['keterangan'],
            "metode"	 => $rows['metode'],
            "jumlah"	 => $rows['nominal']

        );
    }
}
else
{
	$pengeluaran = null;
}

$transaksi[] = array(
        "pendapatan"    => $pendapatan,
        "pengeluaran"   => $pengeluaran
    );

echo json_encode($transaksi);

?>