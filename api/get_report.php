<?php
include('kon.php');

$koneksi = mysqli_connect($host, $user, $pass, $daba);

$kodekost = $_POST['_kodekost'];

if( !$koneksi)
{
    return;
}

// get data persentase kamar terisi, baik persentase maupun frekuensi
$datakamar = array();
$kamarterisi = array();
$kI = 0;
$qryKamarTerisi = "SELECT COUNT(kode_kamar) AS kamarterisi

                   FROM tb_sewa_kamar
                   
                   INNER JOIN tb_kamar
                   ON tb_sewa_kamar.kode_kamar=tb_kamar.id
                   
                   WHERE tb_kamar.kode_kost='$kodekost'
                   AND tanggal_selesai IS NULL";
                   
$rslKamarTerisi = mysqli_query($koneksi, $qryKamarTerisi);
if( mysqli_num_rows($rslKamarTerisi) == 1)
{
    while($rows = mysqli_fetch_assoc($rslKamarTerisi)) 
    {
        $kI = $rows['kamarterisi'];
        $kamarterisi = array(
            'terisi' => $rows['kamarterisi']
            );
    }
    array_push($datakamar, $kamarterisi);
}

$kamartersedia = array();
$kS = 0;
$qryKamarTersedia = "SELECT COUNT(id) AS kamartersedia
                     FROM tb_kamar
                     WHERE kode_kost='$kodekost'
                     AND statuskamar = 'Untuk Sewa'";
$rslKamarTersedia = mysqli_query($koneksi, $qryKamarTersedia);
if( mysqli_num_rows($rslKamarTersedia) == 1) 
{
    while($rows = mysqli_fetch_assoc($rslKamarTersedia)) 
    {
        $kS = $rows['kamartersedia'];
        $kamartersedia = array(
            'tersedia' => $rows['kamartersedia']
            );
    }
    array_push($datakamar, $kamartersedia);
}

// isi kamar kosong
array_push($datakamar, array('kamarkosong' => ($kS - $kI)));

// persentase kamar terisi dan kosong
if($kS != 0)
{
    $persentaseisi = $kI/$kS * 100;
    $persentasekosong = 100 - $persentaseisi;
}
else
{
    $persentaseisi = 0;
    $persentasekosong = 0;
}

$pIsi = ConvertKeDuaDesimal($persentaseisi);
$pKosong = ConvertKeDuaDesimal($persentasekosong);

$persentasekamarterisi = array(
    'persentasekamarterisi' => $pIsi
    );
array_push($datakamar, $persentasekamarterisi);
array_push($datakamar, array('persentasekamarkosong' => $pKosong));

// return json_encode(array('infokamar' => $datakamar));

// get frekuensi kategori keluhan, serta persentasenya
// Data Keluhan
$datakeluhan = array();
// Variable Query Keluhan
$qryBanyakKeluhan = "SELECT COUNT(id) AS banyak_keluhan

                    FROM tb_keluhan_list
                    
                    INNER JOIN tb_penyewa
                    ON tb_keluhan_list.user=tb_penyewa.kode
                    
                    WHERE tb_penyewa.kode_kost='$kodekost'";

$qryKeluhanPelaporan = "SELECT COUNT(tb_keluhan_updatetgl.kode)
                        AS banyak_pelaporan

                        FROM tb_keluhan_updatetgl
                        
                        INNER JOIN tb_keluhan_list
                        ON tb_keluhan_updatetgl.kode=tb_keluhan_list.id
                        
                        INNER JOIN tb_penyewa
                        ON tb_keluhan_list.user=tb_penyewa.kode
                    
                        WHERE tb_penyewa.kode_kost='$kodekost'
                        AND tb_keluhan_updatetgl.status='Pelaporan'";

$qryKeluhanDikerjakan = "SELECT COUNT(tb_keluhan_updatetgl.kode)
                        AS banyak_dikerjakan

                        FROM tb_keluhan_updatetgl
                        
                        INNER JOIN tb_keluhan_list
                        ON tb_keluhan_updatetgl.kode=tb_keluhan_list.id
                        
                        INNER JOIN tb_penyewa
                        ON tb_keluhan_list.user=tb_penyewa.kode
                    
                        WHERE tb_penyewa.kode_kost='$kodekost'
                        AND tb_keluhan_updatetgl.status='Dikerjakan'";

$qryKeluhanSelesai = "SELECT COUNT(tb_keluhan_updatetgl.kode) AS banyak_selesai
                      
                      FROM tb_keluhan_updatetgl
                      
                      INNER JOIN tb_keluhan_list
                      ON tb_keluhan_updatetgl.kode=tb_keluhan_list.id
                      
                      INNER JOIN tb_penyewa
                      ON tb_keluhan_list.user=tb_penyewa.kode
                      
                      WHERE tb_penyewa.kode_kost='$kodekost'
                      AND tb_keluhan_updatetgl.status='Selesai'";

// Variable Result Keluhan
$rslBanyakKeluhan = mysqli_query($koneksi, $qryBanyakKeluhan);
$rslKeluhanPelaporan = mysqli_query($koneksi, $qryKeluhanPelaporan);
$rslKeluhanDikerjakan = mysqli_query($koneksi, $qryKeluhanDikerjakan);
$rslKeluhanSelesai = mysqli_query($koneksi, $qryKeluhanSelesai);

// Variable Total Jumlah Keluhan
$totalKeluhan = 0;
$jmlPelaporan = 0;
$jmlDikerjakan = 0;
$jmlSelesai = 0;

// Keluhan Total
if (mysqli_num_rows($rslBanyakKeluhan) > 0)
{
    while($rowsBanyakKeluhan = mysqli_fetch_assoc($rslBanyakKeluhan))
    {
        $totalKeluhan = $rowsBanyakKeluhan['banyak_keluhan'];
    }
}
// Keluhan Pelaporan
if (mysqli_num_rows($rslKeluhanPelaporan) > 0)
{
    while($rowsKeluhanPelaporan = mysqli_fetch_assoc($rslKeluhanPelaporan))
    {
        $jmlPelaporan = $rowsKeluhanPelaporan['banyak_pelaporan'];
    }
}
// Keluhan Dikerjakan
if (mysqli_num_rows($rslKeluhanDikerjakan) > 0)
{
    while($rowsKeluhanDikerjakan = mysqli_fetch_assoc($rslKeluhanDikerjakan))
    {
        $jmlDikerjakan = $rowsKeluhanDikerjakan['banyak_dikerjakan'];
    }
}
// Keluhan Dikerjakan
if (mysqli_num_rows($rslKeluhanSelesai) > 0)
{
    while($rowsKeluhanSelesai = mysqli_fetch_assoc($rslKeluhanSelesai))
    {
        $jmlSelesai = $rowsKeluhanSelesai['banyak_selesai'];
    }
}

// Hitung Keluhan
if($totalKeluhan != 0)
{
    $hitungKeluhanPelaporan = (($jmlPelaporan - (($jmlDikerjakan - $jmlSelesai) + $jmlSelesai))/$totalKeluhan) * 100;
    $hitungKeluhanDikerjakan = (($jmlDikerjakan - $jmlSelesai)/$totalKeluhan) * 100;
    $hitungKeluhanSelesai = ($jmlSelesai/$totalKeluhan)*100;
    
    // Persentasikan Keluhan
    $persentaseKeluhanPelaporan = ConvertKeDuaDesimal($hitungKeluhanPelaporan);
    $persentaseKeluhanDikerjakan = ConvertKeDuaDesimal($hitungKeluhanDikerjakan);
    $persentaseKeluhanSelesai = ConvertKeDuaDesimal($hitungKeluhanSelesai);
}
else
{
    $persentaseKeluhanPelaporan = 0;
    $persentaseKeluhanDikerjakan = 0;
    $persentaseKeluhanSelesai = 0;
}
array_push($datakeluhan, 
    array(
        'total' => $totalKeluhan    
    ),
    array(
        'pelaporan' => $persentaseKeluhanPelaporan
    ),
    array(
        'dikerjakan' => $persentaseKeluhanDikerjakan
    ),
    array(
        'selesai' => $persentaseKeluhanSelesai
    )
);
// get laporan data penyewa
$datapenyewa = array();
// get data penyewa masuk setiap bulannya
$qryPenyewaMasuk = "SELECT CONCAT(MONTHNAME(merge_date),' - ', YEAR(NOW())) AS bulan,
                    COUNT(tanggal_mulai) AS penyewa_masuk, merge_date
                    FROM `tb_sewa_kamar`
                    RIGHT JOIN (
                        SELECT CONCAT(YEAR(NOW()), '-01-01') AS merge_date
                        UNION SELECT CONCAT(YEAR(NOW()), '-02-01') AS merge_date
                        UNION SELECT CONCAT(YEAR(NOW()), '-03-01') AS merge_date
                        UNION SELECT CONCAT(YEAR(NOW()), '-04-01') AS merge_date
                        UNION SELECT CONCAT(YEAR(NOW()), '-05-01') AS merge_date
                        UNION SELECT CONCAT(YEAR(NOW()), '-06-01') AS merge_date
                        UNION SELECT CONCAT(YEAR(NOW()), '-07-01') AS merge_date
                        UNION SELECT CONCAT(YEAR(NOW()), '-08-01') AS merge_date
                        UNION SELECT CONCAT(YEAR(NOW()), '-09-01') AS merge_date
                        UNION SELECT CONCAT(YEAR(NOW()), '-10-01') AS merge_date
                        UNION SELECT CONCAT(YEAR(NOW()), '-11-01') AS merge_date
                        UNION SELECT CONCAT(YEAR(NOW()), '-12-01') AS merge_date
                    ) AS m ON MONTH(m.merge_date)=MONTH(tb_sewa_kamar.tanggal_mulai)
                    INNER JOIN tb_penyewa_kamar
                    ON tb_sewa_kamar.kode_sewa=tb_penyewa_kamar.kode_sewa
                    WHERE kode_pindah IS NULL
                    AND YEAR(merge_date)=YEAR(NOW())
                    GROUP BY MONTH(merge_date)";

$rslPenyewaMasuk = mysqli_query($koneksi, $qryPenyewaMasuk);
if( mysqli_num_rows($rslPenyewaMasuk) > 0) 
{
    while($rows = mysqli_fetch_assoc($rslPenyewaMasuk)) 
    {
        $penyewaMasuk[] = array(
            'bulan'  => $rows['bulan'],
            'jumlah' => $rows['penyewa_masuk']
            );
    }
    array_push($datapenyewa, array('penyewamasuk' => $penyewaMasuk));
}

// get data penyewa keluar
$qryPenyewaKeluar = "SELECT CONCAT(MONTHNAME(merge_date),' - ', YEAR(NOW())) AS bulan,
                    COUNT(tanggal_selesai) AS penyewa_keluar, merge_date
                    FROM `tb_sewa_kamar`
                    RIGHT JOIN (
                        SELECT CONCAT(YEAR(NOW()), '-01-01') AS merge_date
                        UNION SELECT CONCAT(YEAR(NOW()), '-02-01') AS merge_date
                        UNION SELECT CONCAT(YEAR(NOW()), '-03-01') AS merge_date
                        UNION SELECT CONCAT(YEAR(NOW()), '-04-01') AS merge_date
                        UNION SELECT CONCAT(YEAR(NOW()), '-05-01') AS merge_date
                        UNION SELECT CONCAT(YEAR(NOW()), '-06-01') AS merge_date
                        UNION SELECT CONCAT(YEAR(NOW()), '-07-01') AS merge_date
                        UNION SELECT CONCAT(YEAR(NOW()), '-08-01') AS merge_date
                        UNION SELECT CONCAT(YEAR(NOW()), '-09-01') AS merge_date
                        UNION SELECT CONCAT(YEAR(NOW()), '-10-01') AS merge_date
                        UNION SELECT CONCAT(YEAR(NOW()), '-11-01') AS merge_date
                        UNION SELECT CONCAT(YEAR(NOW()), '-12-01') AS merge_date
                    ) AS m ON MONTH(m.merge_date)=MONTH(tb_sewa_kamar.tanggal_selesai)
                    WHERE kode_sewa NOT IN (SELECT kode_sewa FROM tb_pindah_kamar)
                    AND YEAR(merge_date)=YEAR(NOW())
                    GROUP BY MONTH(merge_date)";

$rslPenyewaKeluar = mysqli_query($koneksi, $qryPenyewaKeluar);
if( mysqli_num_rows($rslPenyewaKeluar) > 0) 
{
    while($rows = mysqli_fetch_assoc($rslPenyewaKeluar)) 
    {
        $penyewaKeluar[] = array(
            'bulan'  => $rows['bulan'],
            'jumlah' => $rows['penyewa_keluar']
            );
    }
    array_push($datapenyewa, array('penyewakeluar' => $penyewaKeluar));
}


$queryInformasiSewa = "SELECT nokamar, tb_penyewa.nama,
                      tbv_status_bayar.hari_menuju_bayar,
                      IF(tanggal_pembayaran_selanjutnya = tanggal_pembayaran_sebelumnya,
                      DATE_ADD(tanggal_pembayaran_sebelumnya, INTERVAL -1 MONTH),
                      tanggal_pembayaran_sebelumnya)
                      AS tanggal_pembayaran_sebelumnya,
                      tbv_status_bayar.tanggal_bayar_sebelumnya,
                      tbv_status_bayar.tanggal_pembayaran_selanjutnya,
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
$status = "";                     
$resultInformasiSewa = mysqli_query($koneksi, $queryInformasiSewa);
if(mysqli_num_rows($resultInformasiSewa) > 0)
{
    while($rows = mysqli_fetch_assoc($resultInformasiSewa))
    {
        $tglprevR = strtotime($rows['tanggal_pembayaran_sebelumnya']);
        $tglnextR = strtotime($rows['tanggal_pembayaran_selanjutnya']);
        $datediff = $tglnextR - $tglprevR;
        $totalhari = round($datediff / (60 * 60 * 24)) + 2;
        if($totalhari >= 0)
        {
            $getpercent = (($totalhari - ((int)$rows['hari_menuju_bayar'] + 2)) / $totalhari) * 100;
            $persentase = ConvertKeDuaDesimal($getpercent);
        }
        else
        {
            $persentase = 100;
        }
        
        if((int)$rows['hari_menuju_bayar'] <= 3 && (int)$rows['hari_menuju_bayar'] >= 0)
        {
            $status = "Warning";
        }
        elseif((int)$rows['hari_menuju_bayar'] < 0)
        {
            $status = "Jatuh Tempo";
        }
        elseif((int)$rows['hari_menuju_bayar'] > 3)
        {
            $status = "Aman";
        }
        
        $informasisewa[] = array(
            "nokamar"       => $rows['nokamar'],
            "namapenyewa"   => $rows['nama'],
            "sisahari"      => $rows['hari_menuju_bayar'],
            "status"        => $status,
            "tglprev"       => $rows['tanggal_pembayaran_sebelumnya'],
            "tglnext"       => $rows['tanggal_pembayaran_selanjutnya'],
            "totalhari"     => round($datediff / (60 * 60 * 24)),
            "persentase"    => (int)$persentase,
            "hargaperbulan" => $rows['harga_perbulan'],
            "test"          => $totalhari
        );
    }
}
else
{
	$informasisewa = null;
}

// Report Pendapatan Semua Tanggal
$queryPendapatan = "SELECT DATE_FORMAT(tanggal_bayar,'%Y-%m-%d') AS tanggal,
                   CONCAT('Pendapatan Sewa Kost Kamar ', tb_kamar.nokamar) AS keterangan,
                   metode, total_bayar
                   FROM `tb_bayar_kost`
                   INNER JOIN tb_sewa_kamar ON tb_bayar_kost.kode_sewa=tb_sewa_kamar.kode_sewa
                   INNER JOIN tb_kamar ON tb_sewa_kamar.kode_kamar=tb_kamar.id
                   WHERE kode_kost='$kodekost'
                   ORDER BY tanggal_bayar ASC";

$resultGetPendapatan = mysqli_query($koneksi, $queryPendapatan);
$tgl = "0000-00-00";
$totalbayar = 0;
if(mysqli_num_rows($resultGetPendapatan) > 0)
{
	while($rows = mysqli_fetch_assoc($resultGetPendapatan))
    {
        if($tgl == $rows['tanggal'])
        {
            $totalbayar = $totalbayar + (int)$rows['total_bayar'];
        }
        else
        {
            $totalbayar = 0;
            $totalbayar += (int)$rows['total_bayar'];
            $tgl = $rows['tanggal'];
        }
        
        $pendapatan[] = array(
            "tanggal"	 => $rows['tanggal'],
            "jumlah"	 => $totalbayar

        );
    }
}
else
{
	$pendapatan = null;
}

// array_push semua report
$report = array();
// laporan kamar
array_push($report, array('infokamar' => $datakamar));
// laporan keluhan
array_push($report, array('keluhan' => $datakeluhan));
//  laporan penyewa
array_push($report, array('datapenyewa' => $datapenyewa));

echo json_encode(array('report' => $report, 'informasisewa' => $informasisewa, 'pendapatan' => $pendapatan));

function ConvertKeDuaDesimal($nilai)
{
    return substr($nilai, 0, strpos($nilai, ".") + 4);
}

?>