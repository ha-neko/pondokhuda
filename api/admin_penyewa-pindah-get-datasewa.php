<?php

include ("kon.php");

$koneksi = mysqli_connect($host, $user, $pass, $daba);

$kode_kamar = $_POST['_kodekamar'];

if( !$koneksi)
{
    return;
}

// get data sewa -----------------------------------------
$querygetdatasewa = "SELECT tb_sewa_kamar.kode_sewa,
          tb_penyewa.kode, tb_penyewa.nama,
          
          CONCAT(
              DATE_FORMAT(
                (SELECT tb_bayar_kost.tanggal_pembayaran
                FROM tb_sewa_kamar
                INNER JOIN tb_penyewa_kamar ON tb_sewa_kamar.kode_sewa = tb_penyewa_kamar.kode_sewa
                LEFT JOIN tb_pindah_kamar ON tb_penyewa_kamar.kode_pindah = tb_pindah_kamar.kode_pindah
                INNER JOIN tb_bayar_kost ON tb_bayar_kost.kode_sewa =
                (CASE WHEN  tb_sewa_kamar.kode_sewa IN (SELECT tb_bayar_kost.kode_sewa FROM tb_bayar_kost GROUP BY tb_bayar_kost.kode_sewa)
                THEN tb_sewa_kamar.kode_sewa
                ELSE tb_pindah_kamar.kode_sewa END)
                WHERE tb_sewa_kamar.kode_sewa = 
                    (SELECT tb_sewa_kamar.kode_sewa
                    FROM tb_sewa_kamar
                    WHERE tb_sewa_kamar.kode_kamar = '$kode_kamar'
                    AND tb_sewa_kamar.tanggal_selesai IS NULL LIMIT 0,1)
              ORDER BY tb_bayar_kost.kode_bayar DESC LIMIT 0,1),
              '%d/%m/%Y')
              
              ,' s.d. ',
              
              DATE_FORMAT(
                  DATE_ADD(
                    (SELECT tb_bayar_kost.tanggal_pembayaran FROM tb_sewa_kamar
                    INNER JOIN tb_penyewa_kamar ON tb_sewa_kamar.kode_sewa = tb_penyewa_kamar.kode_sewa
                    LEFT JOIN tb_pindah_kamar ON tb_penyewa_kamar.kode_pindah = tb_pindah_kamar.kode_pindah
                    INNER JOIN tb_bayar_kost ON tb_bayar_kost.kode_sewa =
                    (CASE WHEN  tb_sewa_kamar.kode_sewa IN (SELECT tb_bayar_kost.kode_sewa FROM tb_bayar_kost GROUP BY tb_bayar_kost.kode_sewa)
                    THEN tb_sewa_kamar.kode_sewa
                    ELSE tb_pindah_kamar.kode_sewa END)
                    WHERE tb_sewa_kamar.kode_sewa = 
                        (SELECT tb_sewa_kamar.kode_sewa
                        FROM tb_sewa_kamar
                        WHERE tb_sewa_kamar.kode_kamar = '$kode_kamar'
                        AND tb_sewa_kamar.tanggal_selesai IS NULL LIMIT 0,1)
                        ORDER BY tb_bayar_kost.kode_bayar DESC LIMIT 0,1),
                    INTERVAL 
                        (SELECT tb_bayar_kost.periode_bayar
                        FROM tb_sewa_kamar
                        INNER JOIN tb_penyewa_kamar ON tb_sewa_kamar.kode_sewa = tb_penyewa_kamar.kode_sewa
                        LEFT JOIN tb_pindah_kamar ON tb_penyewa_kamar.kode_pindah = tb_pindah_kamar.kode_pindah
                        INNER JOIN tb_bayar_kost ON tb_bayar_kost.kode_sewa =
                        (CASE WHEN  tb_sewa_kamar.kode_sewa IN (SELECT tb_bayar_kost.kode_sewa FROM tb_bayar_kost GROUP BY tb_bayar_kost.kode_sewa)
                        THEN tb_sewa_kamar.kode_sewa
                        ELSE tb_pindah_kamar.kode_sewa END)
                        WHERE tb_sewa_kamar.kode_sewa =
                            (SELECT tb_sewa_kamar.kode_sewa
                            FROM tb_sewa_kamar
                            WHERE tb_sewa_kamar.kode_kamar = '$kode_kamar'
                            AND tb_sewa_kamar.tanggal_selesai IS NULL LIMIT 0,1)
                        ORDER BY tb_bayar_kost.kode_bayar DESC LIMIT 0,1)
                  MONTH),
              '%d/%m/%Y')) AS periodesewa,
          
          DATE_FORMAT(tbv_status_bayar.tanggal_pembayaran_selanjutnya, '%d/%m/%Y') AS tanggal_pembayaran_selanjutnya,
          tb_sewa_kamar.harga_perbulan,
          tb_sewa_kamar.periode_bayar
          FROM tb_sewa_kamar
          INNER JOIN tbv_status_bayar ON tbv_status_bayar.kode_sewa=tb_sewa_kamar.kode_sewa
          INNER JOIN tb_penyewa_kamar ON tb_penyewa_kamar.kode_sewa=tb_sewa_kamar.kode_sewa
          INNER JOIN tb_penyewa ON tb_penyewa_kamar.kode_penyewa=tb_penyewa.kode
          INNER JOIN tb_kamar ON tb_sewa_kamar.kode_kamar=tb_kamar.id
          WHERE tb_sewa_kamar.kode_kamar='$kode_kamar' AND tb_sewa_kamar.tanggal_selesai IS NULL";
           
$resultgetdatasewa = mysqli_query($koneksi, $querygetdatasewa);

if( mysqli_num_rows($resultgetdatasewa) > 0)
{
    while($rows = mysqli_fetch_assoc($resultgetdatasewa))
    {
        $datasewa[] = array(
            "kode_sewa"      => $rows['kode_sewa'],
            "kode_penyewa"   => $rows['kode'],
            "nama_penyewa"   => $rows['nama'],
            "periode_sewa"   => $rows['periodesewa'],
            "periode_bayar"  => $rows['periode_bayar'],
            "tgl_pembayaran" => $rows['tanggal_pembayaran_selanjutnya'],
            "harga_perbulan" => $rows['harga_perbulan'],
            );
    }
    
    echo json_encode(array("datasewa" => $datasewa));
    
}
else
{
    echo json_encode(array("datasewa" => null));
}

?>