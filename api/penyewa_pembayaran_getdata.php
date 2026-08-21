<?php

include ("kon.php");

$kode_penyewa = $_POST['_kodepenyewa'];
$nomorpin = $_POST['_nomorpin'];

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if( !$koneksi)
{
    return;
}

// get data pembayaran client
$finalDataPembayaran = array();
// ---------------------
$datapembayaran = array();

// get data semua penyewa -----------------------------------------
$querygetpembayaran = "SELECT kode_bayar, 
            tb_kamar.nokamar,
            date_sub(tb_bayar_kost.tanggal_pembayaran, INTERVAL 3 day) AS reminderdate,
            DATE_FORMAT(tb_bayar_kost.tanggal_pembayaran, '%d %b %Y') AS tanggal_pembayaran, 
            DATE_FORMAT(tb_bayar_kost.tanggal_bayar,'%d %b %Y') AS tanggal_bayar,
            tb_bayar_kost.periode_bayar, 
            tb_bayar_kost.harga_perbulan,
            DATE_FORMAT(DATE_ADD(tb_bayar_kost.tanggal_pembayaran, INTERVAL 33 day),'%d %b %Y') AS duedatedenda,
            tb_bayar_kost.denda, 
            diskon, total_harga, total_bayar, metode,
            CONCAT(DATE_FORMAT(tb_bayar_kost.tanggal_pembayaran,'%d %b %Y'),' s.d.' , DATE_FORMAT(DATE_ADD(tb_bayar_kost.tanggal_pembayaran, INTERVAL 1 MONTH),'%d %b %Y')) AS periodebulanbayar,
            (tb_bayar_kost.total_harga - (tb_bayar_kost.total_bayar + tb_bayar_kost.total_bayar_sebelumnya)) AS sisabayarsaatitu,
            IF((tb_bayar_kost.total_harga - (tb_bayar_kost.total_bayar + tb_bayar_kost.total_bayar_sebelumnya)) <= 0,'Lunas','Belum Lunas') AS statuspembayaran
            
			FROM tb_bayar_kost
            INNER JOIN tb_sewa_kamar ON tb_sewa_kamar.kode_sewa=tb_bayar_kost.kode_sewa
            INNER JOIN tb_kamar ON tb_sewa_kamar.kode_kamar=tb_kamar.id
            INNER JOIN tb_penyewa_kamar ON tb_sewa_kamar.kode_sewa=tb_penyewa_kamar.kode_sewa
            INNER JOIN tb_penyewa ON tb_penyewa_kamar.kode_penyewa=tb_penyewa.kode
            WHERE tb_penyewa.kode='$kode_penyewa' AND nomorpin='$nomorpin' ORDER BY tb_bayar_kost.kode_bayar DESC, tb_bayar_kost.tanggal_bayar DESC";


$resultgetpembayaran = mysqli_query($koneksi, $querygetpembayaran);

if( mysqli_num_rows($resultgetpembayaran) > 0)
{
    $index = mysqli_num_rows($resultgetpembayaran);
    while($rows = mysqli_fetch_assoc($resultgetpembayaran))
    {
        $index--;
        $datapembayaran[] = array(
            "bayarke" => ($index + 1),
            "kode_bayar"    => $rows['kode_bayar'],
            "nomor_kamar"        => $rows['nokamar'],
            "tanggal_reminder"   => $rows['reminderdate'],
            "tanggal_pembayaran" => $rows['tanggal_pembayaran'],
            "tanggal_bayar"      => $rows['tanggal_bayar'],
            "after_duedate"      => $rows['duedatedenda'],
            "periode_bayar"      => $rows['periode_bayar'],
            "harga_perbulan"     => $rows['harga_perbulan'],
            "denda"              => $rows['denda'],
            "diskon"             => $rows['diskon'],
            "total_harga"        => $rows['total_harga'],
            "total_bayar"        => $rows['total_bayar'],
            "metode"             => $rows['metode'],
            "periodesewa"        => $rows['periodebulanbayar'],
            "sisabayarcurr"      => $rows['sisabayarsaatitu'],
            "statusbayar"        => $rows['statuspembayaran']
        );
    }
}
else
{
    $datapembayaran = null;
}

$querygetnextbayar = "SELECT DATE_FORMAT(tbv_status_bayar.tanggal_bayar_sebelumnya,'%d %b %Y') AS tanggal_bayar_sebelumnya, 
IF( status_bayar = 'Belum Lunas', sisa_bayar_sebelumnya, harga_perbulan + denda) AS abc,           
DATE_FORMAT(DATE_ADD(
tbv_status_bayar.tanggal_pembayaran_selanjutnya, INTERVAL 0 MONTH),'%d %b %Y') AS tanggal_pembayaran_selanjutnya,

DATEDIFF(DATE_ADD(tbv_status_bayar.tanggal_pembayaran_selanjutnya, INTERVAL 0 MONTH), CURDATE() )AS jatuhtempobulandepan, 

tbv_status_bayar.status_bayar, 
tbv_status_bayar.total_harga_sebelumnya,
tbv_status_bayar.sisa_bayar_sebelumnya, tbv_status_bayar.total_bayar_sebelumnya,
tbv_status_bayar.tanggal_alarm AS Alarm,
CONCAT(DATE_FORMAT(tbv_status_bayar.tanggal_pembayaran_selanjutnya,'%d %b %Y'), ' s.d. ', DATE_FORMAT(DATE_ADD(tbv_status_bayar.tanggal_pembayaran_selanjutnya, 
INTERVAL 1 MONTH),'%d %b %Y')) AS periodebayarbulanselanjutnya,

IF(tbv_status_bayar.sisa_bayar_sebelumnya = 0, CONCAT(DATE_FORMAT(tbv_status_bayar.tanggal_pembayaran_sebelumnya,'%d %b %Y'),
' s.d. ', 
DATE_FORMAT(DATE_ADD(tbv_status_bayar.tanggal_pembayaran_sebelumnya, INTERVAL tbv_status_bayar.periode_bayar_sebelumnya MONTH),'%d %b %Y')), 
CONCAT(DATE_FORMAT(tbv_status_bayar.tanggal_pembayaran_selanjutnya,'%d %b %Y'),' s.d. ', 
DATE_FORMAT(DATE_ADD(tbv_status_bayar.tanggal_pembayaran_selanjutnya, INTERVAL tbv_status_bayar.periode_bayar_sebelumnya MONTH),'%d %b %Y'))) AS resumetagihan

FROM tbv_status_bayar
INNER JOIN tb_penyewa_kamar ON tb_penyewa_kamar.kode_sewa = tbv_status_bayar.kode_sewa
INNER JOIN tb_penyewa ON tb_penyewa_kamar.kode_penyewa = tb_penyewa.kode
WHERE tb_penyewa_kamar.kode_penyewa = '$kode_penyewa' AND tb_penyewa.nomorpin = '$nomorpin'";

$resultgetnextbayar = mysqli_query($koneksi, $querygetnextbayar);
if( mysqli_num_rows($resultgetnextbayar) == 1)
{
    while($rows = mysqli_fetch_assoc($resultgetnextbayar))
    {
        // Status tagihan yang sadar tanggal jatuh tempo (selaras dengan dashboard web):
        // melewati jatuh tempo -> "Jatuh Tempo", mendekati (<= 3 hari) -> "Warning",
        // selain itu pakai kelengkapan pembayaran invoice terakhir (Lunas/Belum Lunas).
        $sisaHariKeJatuhTempo = (int)$rows['jatuhtempobulandepan'];
        if ($sisaHariKeJatuhTempo < 0)
        {
            $statusTagihan = "Jatuh Tempo";
        }
        else if ($sisaHariKeJatuhTempo <= 3)
        {
            $statusTagihan = "Warning";
        }
        else
        {
            $statusTagihan = $rows['status_bayar'];
        }

        $finalDataPembayaran = array(
            "kumulasi" => $rows['abc'],
            "tanggalbayar" => $rows['tanggal_bayar_sebelumnya'],
            "nexttglbayar" => $rows['tanggal_pembayaran_selanjutnya'],
            "sisaharibayar" => $rows['jatuhtempobulandepan'],
            "statusbayar" => $statusTagihan,
            "tagihantotal" => "Rp. " . $rows['total_harga_sebelumnya'],
            "sisabayarsebelumnya" => "Rp. " . $rows['sisa_bayar_sebelumnya'],
            "bayarsebelumnya" => "Rp. " . $rows['total_bayar_sebelumnya'],
            "alarm" => $rows['Alarm'],
            "resumetagihan" => $rows['resumetagihan'],
            "periodebayarbulan" => $rows['periodebayarbulanselanjutnya'],
            "historibayar" => $datapembayaran
            );
    }
    
    // echo "kumulasi " . $finalDataPembayaran['kumulasi'] . "\n";
    // echo "metode " . $finalDataPembayaran['historibayar'][0]['metode'];
    $dp_size = sizeof($finalDataPembayaran['historibayar']) - 1;
    $lamahari = $finalDataPembayaran['sisaharibayar'];
    
    // Text
    $line1Judul = "";
    $line2SisaHari = abs($lamahari);
    $line3TextHari = "";
    $line4Tgl1 = "";
    $line5Tgl2 = $finalDataPembayaran['nexttglbayar'];
    $line6Sebesar = $finalDataPembayaran['kumulasi'];
    $line7BayarUntukPeriode = $finalDataPembayaran['periodebayarbulan'];
    $textLine7 = "";
    
    if( $lamahari > 0)
    {
        $line1Judul = "Jatuh Tempo Menuju Pembayaran\nPeriode Selanjutnya\n" . $line7BayarUntukPeriode;
        $textLine7 = "Pada Periode Sewa " . $line7BayarUntukPeriode;
        $line3TextHari = "hari lagi";
        $line4Tgl1 = "ke Tanggal ";
    }
    else if( $lamahari == 0)
    {
        $line1Judul = "Hari Ini Adalah\nPembayaran Terakhir Anda\ndi Periode " . $line7BayarUntukPeriode;
        $textLine7 = "Pada Periode Sewa " . $line7BayarUntukPeriode;
        $line3TextHari = "";
        $line4Tgl1 = "tanggal ";
    }
    else if( $lamahari > -3)
    {
        $line1Judul = "Anda Sedang Dalam Waktu\nJatuh Tempo Denda Pembayaran\nPeriode " . $line7BayarUntukPeriode;
        $textLine7 = "Pada Periode Sewa " . $line7BayarUntukPeriode;
        $line2SisaHari = 3 - $line2SisaHari;
        $line3TextHari = "hari lagi";
        $line4Tgl1 = "Ke Tanggal ";
        $line5Tgl2 = $finalDataPembayaran['historibayar'][$dp_size]['after_duedate'];
    }
    else if( $lamahari == -3)
    {
        $line1Judul = "Jatuh Tempo Pembayaran Anda\nTelah Habis di Periode " . $line7BayarUntukPeriode;
        $textLine7 = "Pada Periode Sewa " . $line7BayarUntukPeriode;
        $line2SisaHari = "0";
        $line3TextHari = "hari";
        $line4Tgl1 = "di Tanggal ";
        $line5Tgl2 = $finalDataPembayaran['historibayar'][$dp_size]['after_duedate'];
    }
    else if( $lamahari < -3)
    {
        $line1Judul = "Anda Telat Melakukan Pembayaran\nSewa Kost untuk Periode\n" . $line7BayarUntukPeriode . "\nSelama";
        $line2SisaHari = $line2SisaHari - 3;
        $line3TextHari = "hari";
        $line4Tgl1 = "dari Tanggal ";
        $textLine7 = "Pada Periode Sewa " . $line7BayarUntukPeriode;
        $line5Tgl2 = $finalDataPembayaran['historibayar'][$dp_size]['after_duedate'];
    }
    
    $keteranganResume = array(
        "line1" => $line1Judul,
        "line2" => $line2SisaHari,
        "line3" => $line3TextHari,
        "line4" => $line4Tgl1 . $line5Tgl2,
        "line5" => "Sebesar Rp. " . $line6Sebesar,
        "line6" => $line7BayarUntukPeriode,
        "remindertgl" => $finalDataPembayaran['alarm']
        );
        
    $lemparJSON = json_encode(array('personalinfobayar' => $finalDataPembayaran, 'resumepembayaran' => $keteranganResume));
    echo $lemparJSON;
}
else
{
    // $finalDataPembayaran[] = null;
    echo "user tidak diketahui";
}

?>