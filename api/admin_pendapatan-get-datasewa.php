<?php

include ("kon.php");

$koneksi = mysqli_connect($host, $user, $pass, $daba);

$kode_kamar = $_POST['_kodekamar'];
$no_kamar = $_POST['_nokamar'];
$kode_kost = $_POST['_kodekost'];

if( !$koneksi)
{
    return;
}

// get data sewa -----------------------------------------
$querygetdatabayarsewa = "SELECT tbv_status_bayar.kode_sewa, tb_kamar.nokamar,
        tb_penyewa.nama, tb_penyewa.noktp, tbv_status_bayar.tanggal_pembayaran_selanjutnya,
        tb_sewa_kamar.periode_bayar, tb_sewa_kamar.harga_perbulan,
        tbv_status_bayar.denda_sebelumnya, tbv_status_bayar.diskon_sebelumnya,
        tbv_status_bayar.harga_pindah_sebelumnya,
        tbv_status_bayar.total_harga_sebelumnya,
        tbv_status_bayar.total_bayar_sebelumnya,
        tbv_status_bayar.sisa_bayar_sebelumnya, tbv_status_bayar.denda, tbv_status_bayar.status_bayar,
        IF(tbv_status_bayar.status_bayar = 'Lunas',
        CONCAT(
            DATE_FORMAT(tbv_status_bayar.tanggal_pembayaran_selanjutnya, '%d/%m/%Y'),
            ' s.d ',
            DATE_FORMAT(
            DATE_ADD(tbv_status_bayar.tanggal_pembayaran_selanjutnya,
            INTERVAL (SELECT periode_bayar FROM tb_sewa_kamar WHERE kode_kamar = '$kode_kamar' AND tanggal_selesai IS NULL) MONTH),
            '%d/%m/%Y')),
        CONCAT(DATE_FORMAT(
          (SELECT tb_bayar_kost.tanggal_pembayaran FROM tb_bayar_kost WHERE tb_bayar_kost.kode_sewa = 
          (SELECT tb_sewa_kamar.kode_sewa FROM tb_sewa_kamar WHERE tb_sewa_kamar.kode_kamar = '$kode_kamar' AND tb_sewa_kamar.tanggal_selesai IS NULL LIMIT 0,1)
          ORDER BY tb_bayar_kost.kode_bayar DESC LIMIT 0,1),'%d/%m/%Y')
            ,' s.d. ',
            DATE_FORMAT(
            DATE_ADD((SELECT tb_bayar_kost.tanggal_pembayaran FROM tb_bayar_kost WHERE tb_bayar_kost.kode_sewa =
            (SELECT tb_sewa_kamar.kode_sewa FROM tb_sewa_kamar WHERE tb_sewa_kamar.kode_kamar = '$kode_kamar' AND tb_sewa_kamar.tanggal_selesai IS NULL LIMIT 0,1)
            ORDER BY tb_bayar_kost.kode_bayar DESC LIMIT 0,1),
                INTERVAL (SELECT tb_bayar_kost.periode_bayar FROM tb_bayar_kost WHERE tb_bayar_kost.kode_sewa =
                (SELECT tb_sewa_kamar.kode_sewa FROM tb_sewa_kamar WHERE tb_sewa_kamar.kode_kamar = '$kode_kamar' AND tb_sewa_kamar.tanggal_selesai IS NULL LIMIT 0,1)
                ORDER BY tb_bayar_kost.kode_bayar DESC LIMIT 0,1)
                MONTH),'%d/%m/%Y')
            )
        ) AS periodesewa
        
        FROM tbv_status_bayar
        
        INNER JOIN tb_sewa_kamar ON tbv_status_bayar.kode_sewa=tb_sewa_kamar.kode_sewa
        INNER JOIN tb_penyewa_kamar ON tb_penyewa_kamar.kode_sewa=tb_sewa_kamar.kode_sewa
        INNER JOIN tb_penyewa ON tb_penyewa_kamar.kode_penyewa=tb_penyewa.kode
        INNER JOIN tb_kamar ON tb_sewa_kamar.kode_kamar=tb_kamar.id
        WHERE tb_sewa_kamar.kode_kamar='$kode_kamar'
        AND tb_kamar.kode_kost='$kode_kost'
        AND tb_sewa_kamar.tanggal_selesai IS NULL";
           
$resultgetdatabayarsewa = mysqli_query($koneksi, $querygetdatabayarsewa);

if( mysqli_num_rows($resultgetdatabayarsewa) > 0)
{
    while($rows = mysqli_fetch_assoc($resultgetdatabayarsewa))
    {
        $databayarsewa[] = array(
            "kode_sewa"               => $rows['kode_sewa'],
            "nokamar"                 => $rows['nokamar'],
            "nama"                    => $rows['nama'],
            "noktp"                   => $rows['noktp'],
            "tanggal_pembayaran"      => $rows['tanggal_pembayaran_selanjutnya'],
            "periode_bayar"           => $rows['periode_bayar'],
            "harga"                   => $rows['harga_perbulan'],
            "denda_sebelumnya"        => $rows['denda_sebelumnya'],
            "diskon_sebelumnya"       => $rows['diskon_sebelumnya'],
            "harga_pindah_sebelumnya" => $rows['harga_pindah_sebelumnya'],
            "total_harga_sebelumnya"  => $rows['total_harga_sebelumnya'],
            "total_bayar_sebelumnya"  => $rows['total_bayar_sebelumnya'],
            "sisa_bayar"              => $rows['sisa_bayar_sebelumnya'],
            "denda"                   => $rows['denda'],
            "status"                  => $rows['status_bayar'],
            "periodesewa"             => $rows['periodesewa'],
        );
    }
        
    echo json_encode(array("databayarsewa" => $databayarsewa));
    
}
else
{
    echo json_encode(array("databayarsewa" => "Error data sewa: ".mysqli_error($koneksi)));
}

?>