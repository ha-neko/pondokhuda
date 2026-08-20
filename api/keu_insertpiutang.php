<?php

include ("kon.php");

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if( !$koneksi)
{
  return;
}

$tanggal_bayar = date("Y-m-d");

// INSERT LAPORAN PIUTANG -----------------------------------------
$queryinsertlappiutang = "INSERT INTO tb_keu_jurnal_umum
                          (kode_transaksi, kode_akun, tanggal, keterangan, posisi, jumlah)
                          SELECT CONCAT('PG-', DATE_FORMAT(NOW(),'%Y/%m/'), tb_kamar.nokamar) AS kode_piutang,
                          '1-1300' AS kode_akun,
                          tanggal_pembayaran_selanjutnya AS tanggal_piutang,
                          CONCAT('Piutang Sewa Kamar ', tb_kamar.nokamar) AS keterangan, 'Debit',
                          IF(sisa_bayar_sebelumnya <> 0,
                          sisa_bayar_sebelumnya, tb_sewa_kamar.harga_perbulan) AS harga
                          FROM tbv_status_bayar
                          INNER JOIN tb_sewa_kamar ON tbv_status_bayar.kode_sewa=tb_sewa_kamar.kode_sewa
                          INNER JOIN tb_kamar ON tb_sewa_kamar.kode_kamar=tb_kamar.id
                          WHERE hari_menuju_bayar = -1
                          AND CONCAT('PG-', DATE_FORMAT(NOW(),'%Y/%m/'), tb_kamar.nokamar) NOT IN
                          (SELECT kode_transaksi FROM tb_keu_jurnal_umum WHERE posisi='Debit');
                          
                          INSERT INTO tb_keu_jurnal_umum
                          (kode_transaksi, kode_akun, tanggal, keterangan, posisi, jumlah)
                          SELECT CONCAT('PG-', DATE_FORMAT(NOW(),'%Y/%m/'), tb_kamar.nokamar) AS kode_piutang,
                          '5-1100' AS kode_akun,
                          tanggal_pembayaran_selanjutnya AS tanggal_piutang,
                          CONCAT('Pendapatan Sewa Kamar ', tb_kamar.nokamar) AS keterangan, 'Kredit',
                          IF(sisa_bayar_sebelumnya <> 0,
                          sisa_bayar_sebelumnya, tb_sewa_kamar.harga_perbulan) AS harga
                          FROM tbv_status_bayar
                          INNER JOIN tb_sewa_kamar ON tbv_status_bayar.kode_sewa=tb_sewa_kamar.kode_sewa
                          INNER JOIN tb_kamar ON tb_sewa_kamar.kode_kamar=tb_kamar.id
                          WHERE hari_menuju_bayar = -1
                          AND CONCAT('PG-', DATE_FORMAT(NOW(),'%Y/%m/'), tb_kamar.nokamar) NOT IN
                          (SELECT kode_transaksi FROM tb_keu_jurnal_umum WHERE posisi='Kredit')";
                          
$resultinsertlappiutang = mysqli_multi_query($koneksi, $queryinsertlappiutang);

if($resultinsertlappiutang)
{
    $datapiutang = 'data piutang berhasil diinput';
}
else
{
    $datapiutang = 'laporan jurnal piutang gagal diinput, Error: '. mysqli_error($koneksi);
}

$hasilInputPiutang = array('piutang' => $datapiutang);
echo json_encode($hasilInputPiutang);

?>