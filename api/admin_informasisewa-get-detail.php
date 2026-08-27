<?php

include ("kon.php");

$koneksi = mysqli_connect($host, $user, $pass, $daba);

$kodekost = $_POST['_kodekost'];
$kodekamar = $_POST['_kodekamar'];

if (!$koneksi)
{
    header('Content-Type: application/json');
    echo json_encode(array('informasisewa' => 'koneksi database gagal'));
    return;
}

$querygetinformasisewa = "SELECT nokamar, tb_penyewa.nama,
                          IF(tbv_status_bayar.status_bayar = 'Lunas',
                          CONCAT(
                          DATE_FORMAT(
                          tbv_status_bayar.tanggal_pembayaran_sebelumnya,
                          '%d/%m/%Y'),
                          ' s.d ',
                          DATE_FORMAT(
                          tbv_status_bayar.tanggal_pembayaran_selanjutnya,
                          '%d/%m/%Y')),
                          CONCAT(
                          DATE_FORMAT(
                          tbv_status_bayar.tanggal_pembayaran_selanjutnya,
                          '%d/%m/%Y'),
                          ' s.d ',
                          DATE_FORMAT(
                          DATE_ADD(
                          tbv_status_bayar.tanggal_pembayaran_selanjutnya,
                          INTERVAL
                          tbv_status_bayar.periode_bayar_sebelumnya
                          MONTH)
                          ,
                          '%d/%m/%Y'))
                          ) AS periode_sewa,
                          tbv_status_bayar.tanggal_pembayaran_selanjutnya,
                          tbv_status_bayar.tanggal_bayar_sebelumnya,
                          tbv_status_bayar.hari_menuju_bayar,
                          tbv_status_bayar.denda,
                          sewa_kamar.harga_perbulan,
                          tbv_status_bayar.denda_sebelumnya,
                          tbv_status_bayar.diskon_sebelumnya,
                          tbv_status_bayar.harga_pindah_sebelumnya,
                          tbv_status_bayar.total_harga_sebelumnya,
                          tbv_status_bayar.total_bayar_sebelumnya,
                          tbv_status_bayar.sisa_bayar_sebelumnya,
                          tbv_status_bayar.status_bayar
                          
                          FROM tb_kamar
                          
                          INNER JOIN (
                                SELECT * FROM tb_sewa_kamar
                                WHERE tanggal_selesai IS NULL
                          ) sewa_kamar
                          ON sewa_kamar.kode_kamar=tb_kamar.id
                          
                          INNER JOIN tbv_status_bayar
                          ON sewa_kamar.kode_sewa=tbv_status_bayar.kode_sewa
                          
                          INNER JOIN tb_penyewa_kamar
                          ON sewa_kamar.kode_sewa=tb_penyewa_kamar.kode_sewa
                          
                          INNER JOIN tb_penyewa
                          ON tb_penyewa_kamar.kode_penyewa=tb_penyewa.kode
                          
                          WHERE tb_kamar.kode_kost='$kodekost'
                          
                          AND tb_kamar.id='$kodekamar'
                          
                          ORDER BY CAST(nokamar AS INTEGER) ASC";

$resultgetinformasisewa = mysqli_query($koneksi, $querygetinformasisewa);

if(mysqli_num_rows($resultgetinformasisewa) > 0)
{
	while($rows = mysqli_fetch_assoc($resultgetinformasisewa))
    {
        $informasisewa[] = array(
            "nokamar"           => $rows['nokamar'],
            "namapenyewa"       => $rows['nama'],
            "periodesewa"       => $rows['periode_sewa'],
            "nextbayar"         => $rows['tanggal_pembayaran_selanjutnya'],
            "tglbayarprev"      => $rows['tanggal_bayar_sebelumnya'],
            "sisahari"          => $rows['hari_menuju_bayar'],
            "sisabayar"         => $rows['sisa_bayar_sebelumnya'],
            "denda"             => $rows['denda'],
            "hargaperbulan"     => $rows['harga_perbulan'],
            "dendaprev"         => $rows['denda_sebelumnya'],
            "diskonprev"        => $rows['diskon_sebelumnya'],
            "pindahprev"        => $rows['harga_pindah_sebelumnya'],
            "totalhargaprev"    => $rows['total_harga_sebelumnya'],
            "totalbayarprev"    => $rows['total_bayar_sebelumnya'],
            "sisabayar"         => $rows['sisa_bayar_sebelumnya'],
            "status"            => $rows['status_bayar'],
        );
    }
}
else
{
	$informasisewa = null;
}

echo json_encode(array("informasisewa" => $informasisewa));

?>