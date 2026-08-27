<?php

include ("kon.php");

$koneksi = mysqli_connect($host, $user, $pass, $daba);

$kodekost = $_POST['_kodekost'];
$jumlahpenyewa = $_POST['_jmlpenyewa'];
// $jumlahpenyewa = 1;

if (!$koneksi)
{
    header('Content-Type: application/json');
    echo json_encode(array('kamar' => 'koneksi database gagal'));
    return;
}

$hasil;

if($jumlahpenyewa > 1)
{
    $querygetkamar = "SELECT tb_kamar.id, nokamar, penyewa_kamar.jumlah_penyewa,
    tbv_status_bayar.tanggal_pembayaran_selanjutnya, tbv_status_bayar.status_bayar

    FROM `tb_kamar`
    
    LEFT JOIN (
        SELECT *
        FROM tb_sewa_kamar
        WHERE tanggal_selesai IS NULL
    ) sewa_kamar ON tb_kamar.id=sewa_kamar.kode_kamar
    
    LEFT JOIN (
        SELECT tb_sewa_kamar.kode_sewa, COUNT(kode_penyewa) AS jumlah_penyewa
        FROM tb_sewa_kamar
        INNER JOIN tb_penyewa_kamar ON tb_penyewa_kamar.kode_sewa=tb_sewa_kamar.kode_sewa
        WHERE tanggal_selesai IS NULL
        GROUP BY kode_sewa
    ) penyewa_kamar ON penyewa_kamar.kode_sewa=sewa_kamar.kode_sewa
    
    LEFT JOIN tbv_status_bayar ON tbv_status_bayar.kode_sewa=sewa_kamar.kode_sewa
    
    WHERE statuskamar='Untuk Sewa'
    AND jumlah_penyewa IS NULL
    AND tb_kamar.kode_kost='$kodekost'
    ORDER BY CAST(nokamar AS INTEGER) ASC";
                    
    $resultgetkamar = mysqli_query($koneksi, $querygetkamar);
    if( mysqli_num_rows($resultgetkamar) > 0)
    {
        while($rows = mysqli_fetch_assoc($resultgetkamar))
        {
            $kamar[] = array(
                "kodekamar" => $rows['id'],
                "nokamar" => $rows['nokamar'],
                "jumlahpenyewa" => $rows['jumlah_penyewa'],
                "tglpembayaran" => $rows['tanggal_pembayaran_selanjutnya'],
                "statusbayar" => $rows['status_bayar']
            );
        }
    }
    else
    {
        $kamar = null;
    }
}
else
{
    $querygetkamar = "SELECT tb_kamar.id, nokamar, penyewa_kamar.jumlah_penyewa,
    tbv_status_bayar.tanggal_pembayaran_selanjutnya, tbv_status_bayar.status_bayar

    FROM `tb_kamar`
    
    LEFT JOIN (
        SELECT *
        FROM tb_sewa_kamar
        WHERE tanggal_selesai IS NULL
    ) sewa_kamar ON tb_kamar.id=sewa_kamar.kode_kamar
    
    LEFT JOIN (
        SELECT tb_sewa_kamar.kode_sewa, COUNT(kode_penyewa) AS jumlah_penyewa
        FROM tb_sewa_kamar
        INNER JOIN tb_penyewa_kamar ON tb_penyewa_kamar.kode_sewa=tb_sewa_kamar.kode_sewa
        WHERE tanggal_selesai IS NULL
        GROUP BY kode_sewa
    ) penyewa_kamar ON penyewa_kamar.kode_sewa=sewa_kamar.kode_sewa
    
    LEFT JOIN tbv_status_bayar ON tbv_status_bayar.kode_sewa=sewa_kamar.kode_sewa
    
    WHERE statuskamar='Untuk Sewa'
    AND (jumlah_penyewa < 2 OR jumlah_penyewa IS NULL)
    AND tb_kamar.kode_kost='$kodekost'
    ORDER BY CAST(nokamar AS INTEGER) ASC";
                    
    $resultgetkamar = mysqli_query($koneksi, $querygetkamar);
    if( mysqli_num_rows($resultgetkamar) > 0)
    {
        while($rows = mysqli_fetch_assoc($resultgetkamar))
        {
            $kamar[] = array(
                "kodekamar" => $rows['id'],
                "nokamar" => $rows['nokamar'],
                "jumlahpenyewa" => $rows['jumlah_penyewa'],
                "tglpembayaran" => $rows['tanggal_pembayaran_selanjutnya'],
                "statusbayar" => $rows['status_bayar']
            );
        }
    }
    else
    {
        $kamar = null;
    }
}

echo json_encode(array('kamar' => $kamar));

?>