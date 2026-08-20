<?php

include ("kon.php");
//include ("admin_log.php");

$koneksi = mysqli_connect($host, $user, $pass, $daba);

$kodekost = $_POST['_kodekost'];

if( !$koneksi)
{
    return;
}

$querygetinformasisewa = "SELECT tb_kamar.id, tb_kamar.nokamar, tb_penyewa.nama,
                          DATE_FORMAT(tbv_status_bayar.tanggal_pembayaran_selanjutnya, '%d/%m/%Y') AS tanggal_pembayaran_selanjutnya,
                          tbv_status_bayar.hari_menuju_bayar,
                          tbv_status_bayar.sisa_bayar_sebelumnya,
                          tbv_status_bayar.denda,
                          sewa_kamar.harga_perbulan
                          FROM tb_kamar
                          INNER JOIN (
                                SELECT * FROM tb_sewa_kamar
                                WHERE tanggal_selesai IS NULL
                          ) sewa_kamar ON sewa_kamar.kode_kamar=tb_kamar.id
                          INNER JOIN tbv_status_bayar ON sewa_kamar.kode_sewa=tbv_status_bayar.kode_sewa
                          INNER JOIN tb_penyewa_kamar ON sewa_kamar.kode_sewa=tb_penyewa_kamar.kode_sewa
                          INNER JOIN tb_penyewa ON tb_penyewa_kamar.kode_penyewa=tb_penyewa.kode
                          WHERE tb_kamar.kode_kost='$kodekost'
                          ORDER BY CAST(nokamar AS INTEGER) ASC";

$resultgetinformasisewa = mysqli_query($koneksi, $querygetinformasisewa);

if(mysqli_num_rows($resultgetinformasisewa) > 0)
{
	while($rows = mysqli_fetch_assoc($resultgetinformasisewa))
    {
        $informasisewa[] = array(
            "id"            => $rows['id'],
            "nokamar"       => $rows['nokamar'],
            "namapenyewa"   => $rows['nama'],
            "sisahari"      => $rows['hari_menuju_bayar'],
            "nextbayar"     => $rows['tanggal_pembayaran_selanjutnya'],
            "sisabayar"     => $rows['sisa_bayar_sebelumnya'],
            "denda"         => $rows['denda'],
            "hargaperbulan" => $rows['harga_perbulan']
        );
    }
}
else
{
	$informasisewa = null;
}
//$log = CreateLog($koneksi, 'Liat Info Sewa', $kodekost);
echo json_encode(array("info" => $informasisewa));

?>