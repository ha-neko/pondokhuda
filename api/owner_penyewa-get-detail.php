<?php

include ("kon.php");

$koneksi = mysqli_connect($host, $user, $pass, $daba);

$kode = $_POST['_kode'];
$kodekost = $_POST['_kodekost'];

if (!$koneksi)
{
    header('Content-Type: application/json');
    echo json_encode(array('penyewa' => 'koneksi database gagal'));
    return;
}

// get data semua penyewa -----------------------------------------
$querygetallpenyewa = "SELECT tb_penyewa.kode, tb_penyewa.nama,
                       tb_penyewa.status, tb_kamar.nokamar,
                       tb_penyewa.urlfoto,
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
                          tb_sewa_kamar.harga_perbulan,
                          tbv_status_bayar.denda_sebelumnya,
                          tbv_status_bayar.diskon_sebelumnya,
                          tbv_status_bayar.harga_pindah_sebelumnya,
                          tbv_status_bayar.total_harga_sebelumnya,
                          tbv_status_bayar.total_bayar_sebelumnya,
                          tbv_status_bayar.sisa_bayar_sebelumnya,
                          tbv_status_bayar.status_bayar
                          
                          FROM tb_penyewa
                          
                          INNER JOIN tb_penyewa_kamar
                          ON tb_penyewa.kode=tb_penyewa_kamar.kode_penyewa
                          
                          INNER JOIN (
                          SELECT * FROM tb_sewa_kamar WHERE tanggal_selesai IS NULL
                          )tb_sewa_kamar
                          ON tb_penyewa_kamar.kode_sewa=tb_sewa_kamar.kode_sewa
                          
                          INNER JOIN tb_kamar
                          ON tb_sewa_kamar.kode_kamar=tb_kamar.id
                          
                          INNER JOIN tbv_status_bayar
                          ON tb_sewa_kamar.kode_sewa=tbv_status_bayar.kode_sewa
                          
                          INNER JOIN tb_bayar_kost
                          ON tb_sewa_kamar.kode_sewa=tb_bayar_kost.kode_sewa
                          
                          WHERE tb_penyewa.kode='$kode'
                          
                          AND tb_penyewa.kode_kost='$kodekost'
                          
                          AND tb_bayar_kost.kode_bayar=(
						    SELECT MAX(kode_bayar)
						    FROM tb_bayar_kost
						    
						    INNER JOIN (
						        SELECT * FROM tb_sewa_kamar
						        WHERE tanggal_selesai IS NULL
						    )
						    tb_sewa_kamar
						    ON tb_bayar_kost.kode_sewa=tb_sewa_kamar.kode_sewa
						    
						    INNER JOIN tb_penyewa_kamar
						    ON tb_sewa_kamar.kode_sewa=tb_penyewa_kamar.kode_sewa
						    
						    WHERE kode_penyewa='$kode')";
						
$resultgetallpenyewa = mysqli_query($koneksi, $querygetallpenyewa);
if( mysqli_num_rows($resultgetallpenyewa) > 0)
{
    while($rows = mysqli_fetch_assoc($resultgetallpenyewa))
    {
        $penyewa[] = array(
            
            "kode"              => $rows['kode'],
            "nama"              => $rows['nama'],
            "status"            => $rows['status'],
            "nokamar"           => $rows['nokamar'],
            "urlfoto"           => $rows['urlfoto'],
            "nextbayar"         => $rows['tanggal_pembayaran_selanjutnya'],
            "harimenujubayar"   => $rows['hari_menuju_bayar'],
            "sisabayarprev"     => $rows['sisa_bayar_sebelumnya'],
            "hargaperbulan"     => $rows['harga_perbulan'],
            "denda"             => $rows['denda'],
            "tglbayarprev"      => $rows['tanggal_bayar_sebelumnya'],
            "bayarprev"         => $rows['total_bayar_sebelumnya'],
        );
    }
    
}
else
{
    $penyewa = null;
}

echo json_encode(array('penyewa' => $penyewa));

?>