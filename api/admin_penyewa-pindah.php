<?php
include('kon.php');
include ("admin_log.php");
include ("owner_log.php");

$kodesewa = $_POST['_kodesewa'];
$kodepenyewa = $_POST['_kodepenyewa'];
$namapenyewa = $_POST['_namapenyewa'];
$kodekamar = $_POST['_kodekamarbaru'];
$nokamar = $_POST['_nokamarbaru'];
$kamarterisi = $_POST['_kamarterisi'] ;

$kodekamarlama = $_POST['_kodekamarlama'];
$nokamarlama = $_POST['_nokamarlama'];
$tglpembayaranlama = $_POST['_tglpembayaranlama'];
$hargakamarlama = $_POST['_hargakamarlama'] ;
$periodebayarlama = $_POST['_periodebayarlama'] ;

$tanggalpembayaran = $_POST['_tglpembayaranbaru'];
$hargakamar = $_POST['_hargakamarbaru'] ;
$periodebayar = $_POST['_periodebayarbaru'] ;
$diskon = $_POST['_diskon'] ;
$hargapindah = $_POST['_hargapindah'];
$totalharga = $_POST['_totalharga'] ;
$totalbayar = $_POST['_totalbayar'] ;

if(isset($_POST['_kodeadmin']))
{
    $kodeadmin = $_POST['_kodeadmin'];
    $namaadmin = $_POST['_namaadmin'];
}
if(isset($_POST['_kodeowner']))
{
    $kodeowner = $_POST['_kodeowner'];
    $namaowner = $_POST['_namaowner'];
}

$kodekost = $_POST['_kodekost'];

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if (!$koneksi)
{
    header('Content-Type: application/json');
    echo json_encode(array('datapindah' => 'koneksi database gagal'));
    return;
}

date_default_timezone_set("Asia/Bangkok");
// --------------------------------------------------------------------\

// -------------------- Generate kodebayar --------------------------\
$querygetkdbayar = "SELECT kode_bayar
                    FROM tb_bayar_kost
                    JOIN tb_sewa_kamar USING (kode_sewa)
                    INNER JOIN tb_kamar 
                    ON tb_sewa_kamar.kode_kamar = tb_kamar.id
                    WHERE MONTH(tanggal_bayar)=MONTH(NOW())
                    AND YEAR(tanggal_bayar)=YEAR(NOW())
                    AND tb_kamar.kode_kost='$kodekost'
                    ORDER BY SUBSTRING(kode_bayar, -6) DESC LIMIT 0,1";
       
$resultgetkdbayar = mysqli_query($koneksi, $querygetkdbayar);

if(mysqli_num_rows($resultgetkdbayar) > 0)
{
    $kode_pembayaran;
    while($rows = mysqli_fetch_assoc($resultgetkdbayar))
    {
        $kode_bayar_terakhir = $rows['kode_bayar'];
    }
    
    $kodebayar = GenerateKodePembayaran($kode_bayar_terakhir, $nokamar, $kodekost);
}
else
{
    $kodebayar = 'KW-'.date('Y/m/').$nokamar.'/'.$kodekost.'/000001';
}

$tanggalSelesai = date("Y-m-d");
$pindahDropSewa = false;

// ------------------- Bikin Pindah Asal -------------------------\
$queryInsertPindahAsal = "INSERT INTO tb_pindah_kamar(kode_sewa, tanggal_pindah)
                          VALUES ('$kodesewa', '$tanggalSelesai')";

$resultInsertPindahAsal = mysqli_query($koneksi, $queryInsertPindahAsal);

if ($resultInsertPindahAsal)
{
    $kodePindahAsal = mysqli_insert_id($koneksi);

    // ------------------ Drop Sewa Asal ------------------\
    $queryDropSewaAsal = "UPDATE tb_sewa_kamar
                          SET tanggal_selesai='$tanggalSelesai'
                          WHERE kode_sewa='$kodesewa'";

    $resultDropSewaAsal = mysqli_query($koneksi, $queryDropSewaAsal);

    if($resultDropSewaAsal)
    {
        // --------------- Kalo kamar tujuan adaan ---------------\
        if ($kamarterisi == "true")
        {
            // -------------- Bikin Pindah Tujuan --------------\
            $queryInsertPindahTujuan = "INSERT INTO tb_pindah_kamar(kode_sewa, tanggal_pindah)
                          SELECT kode_sewa, '$tanggalSelesai'
                          FROM tb_sewa_kamar
                          WHERE kode_kamar='$kodekamar'
                          AND tanggal_selesai IS NULL";

            $resultInsertPindahTujuan = mysqli_query($koneksi, $queryInsertPindahTujuan);

            if ($resultInsertPindahTujuan)
            {
                $kodePindahTujuan = mysqli_insert_id($koneksi);

                // ------------------ Drop Sewa Tujuan ------------------\
                $queryDropSewaTujuan = "UPDATE tb_sewa_kamar
                               SET tanggal_selesai='$tanggalSelesai'
                               WHERE kode_kamar='$kodekamar'
                               AND tanggal_selesai IS NULL";

                $resultDropSewaTujuan = mysqli_query($koneksi, $queryDropSewaTujuan);

                if($resultDropSewaTujuan)
                {
                    $pindahDropSewa = true;
                }
                else
                {
                    $penyewapindah = "Error drop sewa tujuan: ".mysqli_error($koneksi);           
                }
            }
            else
            {
                $penyewapindah = "Error insert pindah tujuan: ".mysqli_error($koneksi);
            }
        }
        else
        {
            $pindahDropSewa = true;
        }
    }
    else
    {
        $penyewapindah = "Error drop sewa asal: ".mysqli_error($koneksi);
    }
}
else
{
    $penyewapindah = "Error insert pindah asal: ".mysqli_error($koneksi);
}

// ------------------- SEWA KAMAR ------------------- \\
$tanggalMulai = date("Y-m-d");
$sewaKamar = false;

if ($pindahDropSewa)
{
    $queryGetPenyewaPindah = "SELECT kode_penyewa FROM tb_penyewa_kamar
                              WHERE kode_sewa='$kodesewa'";

    $resultGetPenyewaPindah = mysqli_query($koneksi, $queryGetPenyewaPindah);

    if (mysqli_num_rows($resultGetPenyewaPindah) > 0)
    {
        // ------- penyewanya dua -------\
        if (mysqli_num_rows($resultGetPenyewaPindah) > 1)
        {
            // ------- yang ikut satu -------\
            if (count($kodepenyewa) == 1)
            {
                // ----------- Get Penyewa Pindah -----------\
                foreach ($kodepenyewa as $penyewa)
                {
                    if (isset($penyewa))
                    {
                        $penyewaPindah = $penyewa;
                    }
                }

                $queryInsertSewaAsal = "INSERT INTO tb_sewa_kamar(kode_kamar, tanggal_mulai, harga_perbulan, tanggal_pembayaran, periode_bayar)
                    VALUES ('$kodekamarlama', '$tanggalMulai', $hargakamarlama - 200000, '$tglpembayaranlama', '$periodebayarlama')";

                $resultInsertSewaAsal = mysqli_query($koneksi, $queryInsertSewaAsal);

                if ($resultInsertSewaAsal)
                {
                    $kodeSewaAsal = mysqli_insert_id($koneksi);

                    $queryInsertPenyewaGakPindah = "INSERT INTO
                        tb_penyewa_kamar(kode_sewa, kode_penyewa, kode_pindah)
                        SELECT '$kodeSewaAsal', kode_penyewa, '$kodePindahAsal'
                        FROM tb_penyewa_kamar WHERE kode_sewa='$kodesewa'
                        AND kode_penyewa !='$penyewaPindah'";
                    
                    $resultInsertPenyewaGakPindah = mysqli_query($koneksi, $queryInsertPenyewaGakPindah);

                    if ($resultInsertPenyewaGakPindah)
                    {
                        // ------- insert data sewa -------\
                        $queryInsertSewaTujuan = "INSERT INTO tb_sewa_kamar(kode_kamar, tanggal_mulai,
                                                  harga_perbulan, tanggal_pembayaran, periode_bayar)
                                                  VALUES ('$kodekamar', '$tanggalMulai', '$hargakamar',
                                                  '$tanggalpembayaran', '$periodebayar')";

                        $resultInsertSewaTujuan = mysqli_query($koneksi, $queryInsertSewaTujuan);

                        if ($resultInsertSewaTujuan)
                        {
                            $kodeSewaTujuan = mysqli_insert_id($koneksi);

                            // ------- kalo pindah ke kamar adaan -------\
                            if ($kamarterisi == "true")
                            {
                                // ------- insert data penyewa tujuan -------\
                                $queryInsertPenyewaTujuan = "INSERT INTO
                                tb_penyewa_kamar(kode_sewa, kode_penyewa, kode_pindah)
                                SELECT '$kodeSewaTujuan', kode_penyewa, '$kodePindahTujuan'
                                FROM tb_penyewa_kamar WHERE kode_sewa = (
                                SELECT kode_sewa FROM tb_pindah_kamar WHERE kode_pindah='$kodePindahTujuan')";

                                $resultInsertPenyewaTujuan = mysqli_query($koneksi, $queryInsertPenyewaTujuan);

                                if ($resultInsertPenyewaTujuan)
                                {
                                    // ------- insert data penyewa asal (yang pindah) -------\
                                    $queryInsertPenyewaPindah = "INSERT INTO
                                    tb_penyewa_kamar(kode_sewa, kode_penyewa, kode_pindah)
                                    VALUES('$kodeSewaTujuan', '$penyewaPindah', '$kodePindahAsal')";

                                    $resultInsertPenyewaPindah = mysqli_query($koneksi, $queryInsertPenyewaPindah);

                                    if ($resultInsertPenyewaPindah)
                                    {
                                        // berhasil
                                        $sewaKamar = true;
                                    }
                                    else
                                    {
                                        $penyewapindah = "Error insert penyewa pindah asal ke tujuan (1 of 2): ".mysqli_error($koneksi);
                                    }
                                }
                                else
                                {
                                    $penyewapindah = "Error insert penyewa pindah tujuan (1 of 2): ".mysqli_error($koneksi);
                                }
                            }
                            else
                            {
                                // ------- insert data penyewa asal (yang pindah) -------\
                                $queryInsertPenyewaPindah = "INSERT INTO
                                tb_penyewa_kamar(kode_sewa, kode_penyewa, kode_pindah)
                                VALUES('$kodeSewaTujuan', '$penyewaPindah', '$kodePindahAsal')";

                                $resultInsertPenyewaPindah = mysqli_query($koneksi, $queryInsertPenyewaPindah);

                                if ($resultInsertPenyewaPindah)
                                {
                                    // berhasil
                                    $sewaKamar = true;
                                }
                                else
                                {
                                    $penyewapindah = "Error insert penyewa pindah asal ke tujuan (1 of 2): ".mysqli_error($koneksi);
                                }   
                            }
                        }
                        else
                        {
                            $penyewapindah = "Error insert sewa tujuan (1 of 2): ".mysqli_error($koneksi);
                        }
                    }
                    else
                    {
                        $penyewapindah = "Error insert penyewa pindah asal (1 of 2): ".mysqli_error($koneksi);
                    }
                }
                else
                {
                    $penyewapindah = "Error insert sewa asal (1 of 2): ".mysqli_error($koneksi);
                }
            }
            // ------- semua pindah -------\
            else
            {
                // ------- insert data sewa -------\
                $queryInsertSewaTujuan = "INSERT INTO tb_sewa_kamar(kode_kamar, tanggal_mulai,
                                      harga_perbulan, tanggal_pembayaran, periode_bayar)
                                      VALUES ('$kodekamar', '$tanggalMulai', '$hargakamar',
                                      '$tanggalpembayaran', '$periodebayar')";

                $resultInsertSewaTujuan = mysqli_query($koneksi, $queryInsertSewaTujuan);

                if ($resultInsertSewaTujuan)
                {
                    $kodeSewaTujuan = mysqli_insert_id($koneksi);

                    // ------- insert data penyewa asal (yang pindah) -------\
                    $queryInsertPenyewaAsal = "INSERT INTO
                        tb_penyewa_kamar(kode_sewa, kode_penyewa, kode_pindah)
                        SELECT '$kodeSewaTujuan', kode_penyewa, '$kodePindahAsal'
                        FROM tb_penyewa_kamar WHERE kode_sewa = (
                        SELECT kode_sewa FROM tb_pindah_kamar WHERE kode_pindah='$kodePindahAsal')";

                    $resultInsertPenyewaAsal = mysqli_query($koneksi, $queryInsertPenyewaAsal);

                    if ($resultInsertPenyewaAsal)
                    {
                        // berhasil
                        $sewaKamar = true;
                    }
                    else
                    {
                        $penyewapindah = "Error insert penyewa pindah tujuan (2 of 2): ".mysqli_error($koneksi);
                    }
                }
                else
                {
                    $penyewapindah = "Error insert sewa pindah (2 of 2): ".mysqli_error($koneksi);
                }
            } 
        }
        // ------- penyewanya satu -------\
        else
        {
            // ------- insert data sewa -------\
            $queryInsertSewaTujuan = "INSERT INTO tb_sewa_kamar(kode_kamar, tanggal_mulai,
                                      harga_perbulan, tanggal_pembayaran, periode_bayar)
                                      VALUES ('$kodekamar', '$tanggalMulai', '$hargakamar',
                                      '$tanggalpembayaran', '$periodebayar')";

            $resultInsertSewaTujuan = mysqli_query($koneksi, $queryInsertSewaTujuan);

            if ($resultInsertSewaTujuan)
            {
                foreach ($kodepenyewa as $penyewa)
                {
                    if (isset($penyewa))
                    {
                        $penyewaPindah = $penyewa;
                    }
                }

                $kodeSewaTujuan = mysqli_insert_id($koneksi);

                // ------- kalo pindah ke kamar adaan -------\
                if ($kamarterisi == "true")
                {
                    // ------- insert data penyewa tujuan -------\
                    $queryInsertPenyewaTujuan = "INSERT INTO
                    tb_penyewa_kamar(kode_sewa, kode_penyewa, kode_pindah)
                    SELECT '$kodeSewaTujuan', kode_penyewa, '$kodePindahTujuan'
                    FROM tb_penyewa_kamar WHERE kode_sewa = (
                    SELECT kode_sewa FROM tb_pindah_kamar WHERE kode_pindah='$kodePindahTujuan')";

                    $resultInsertPenyewaTujuan = mysqli_query($koneksi, $queryInsertPenyewaTujuan);

                    if ($resultInsertPenyewaTujuan)
                    {
                        // ------- insert data penyewa asal (yang pindah) -------\
                        $queryInsertPenyewaAsal = "INSERT INTO
                        tb_penyewa_kamar(kode_sewa, kode_penyewa, kode_pindah)
                        VALUES('$kodeSewaTujuan', '$penyewaPindah', '$kodePindahAsal')";

                        $resultInsertPenyewaAsal = mysqli_query($koneksi, $queryInsertPenyewaAsal);

                        if ($resultInsertPenyewaAsal)
                        {
                            // berhasil
                            $sewaKamar = true;
                        }
                        else
                        {
                            $penyewapindah = "Error insert penyewa pindah asal (kamar tujuan isi) (1 of 1): ".mysqli_error($koneksi);
                        }
                    }
                    else
                    {
                        $penyewapindah = "Error insert penyewa pindah tujuan (1 of 1): ".mysqli_error($koneksi);
                    }
                }
                // ------- beres pindah ke kamar kosong -------\
                else
                {
                    // ------- insert data penyewa asal (yang pindah) -------\
                    $queryInsertPenyewaAsal = "INSERT INTO tb_penyewa_kamar(kode_sewa, kode_penyewa, kode_pindah)
                                               VALUES('$kodeSewaTujuan', '$penyewaPindah', '$kodePindahAsal')";

                    $resultInsertPenyewaAsal = mysqli_query($koneksi, $queryInsertPenyewaAsal);

                    if ($resultInsertPenyewaAsal)
                    {
                        // berhasil
                        $sewaKamar = true;   
                    }
                    else
                    {
                        $penyewapindah = "Error insert penyewa pindah asal (1 of 1): ".mysqli_error($koneksi);
                    }
                }
            }
            else
            {
                $penyewapindah = "Error insert sewa pindah (1 of 1): ".mysqli_error($koneksi);
            }
        }
    }
    else
    {
        $penyewapindah = "Error get penyewa pindah: ".mysqli_error($koneksi);
    }
}

// ------------------- BAYAR PINDAH ------------------- \\
$pembayaran = false;
if ($sewaKamar)
{
    // input pembayaran pertama
    // ----------- Insert bayar pindah -----------\
    $queryBayarPertama = "INSERT INTO tb_bayar_kost(kode_bayar, kode_sewa, tanggal_pembayaran, tanggal_bayar, periode_bayar, harga_perbulan, diskon, harga_pindah, total_harga, total_bayar, metode)
    VALUES('$kodebayar', '$kodeSewaTujuan', '$tanggalpembayaran', '$tanggalMulai', '$periodebayar', '$hargakamar', '$diskon', '$hargapindah', '$totalharga', '$totalbayar', 'Tunai')";

    $resultBayarPertama = mysqli_query($koneksi, $queryBayarPertama);

    if($resultBayarPertama)
    {
        $pembayaran = true;
    }
    else
    {
        $penyewapindah = "Error bayar sewa: ".mysqli_error($koneksi);
    }
}

// ------------------- JURNAL KEUANGAN ------------------- \\
if($pembayaran)
{
    $queryinsertlaporankeu = "INSERT INTO tb_keu_jurnal_umum
                            (kode_transaksi, kode_akun, tanggal, keterangan,
                            posisi, jumlah, kode_kost)
                            
                            VALUES
                            
                            ('$kodebayar', '1-1100', '$tanggalMulai', 'Kas',
                            'Debit', '$totalbayar', '$kodekost')";
                            
    if($diskon != '0')
    {
        $queryinsertlaporankeu .= ", ('$kodebayar', '1-0100', '$tanggalMulai',
                                  'Diskon', 'Debit', '$diskon', '$kodekost')";
    }
    
    $queryinsertlaporankeu .= ", ('$kodebayar', '5-1100', '$tanggalMulai',
                            CONCAT('Pendapatan Sewa Kamar ', '$nokamar'),
                            'Kredit', ($totalbayar + $diskon) - $hargapindah,
                            '$kodekost')";
                            
    if($hargapindah != 0)
    {
        $queryinsertlaporankeu .= ", ('$kodebayar', '5-2200', '$tanggalMulai',
                                  'Pendapatan Perhari Pindah Kost', 'Kredit',
                                  '$hargapindah', '$kodekost')";
    }

    $resultinsertlaporankeu = mysqli_query($koneksi, $queryinsertlaporankeu);
    
    if($resultinsertlaporankeu)
    {
        // Email Attachment
  $cekemail = "";
  $nama = "";
  $c = true;
  $queryGetEmailPenyewa = "SELECT 
	tb_sewa_kamar.kode_sewa, 
   	tb_bayar_kost.kode_bayar, 
    tb_penyewa.nama, 
    tb_penyewa.email, 
    tb_bayar_kost.harga_perbulan,
    tb_bayar_kost.periode_bayar,
    tb_bayar_kost.denda,
    tb_bayar_kost.diskon,
    tb_bayar_kost.harga_pindah,
    tb_bayar_kost.total_bayar,
    tb_bayar_kost.total_bayar_sebelumnya,
    tb_bayar_kost.sisa_bayar_sebelumnya,
    tb_bayar_kost.total_harga,
    tb_bayar_kost.metode,
    
    IF(
    (SELECT COUNT(*) FROM tb_bayar_kost WHERE tb_bayar_kost.kode_sewa = (SELECT tb_sewa_kamar.kode_sewa FROM tb_sewa_kamar WHERE tb_sewa_kamar.kode_kamar = '$kodekamar' AND tb_sewa_kamar.tanggal_selesai IS NULL LIMIT 0,1)) = 1,
    
    CONCAT('Pembayaran awal di bulan ', 
    IF(MONTH(tb_bayar_kost.periode_bayar)=1,'Januari',
    IF(MONTH(tb_bayar_kost.periode_bayar)=2,'Februari',
    IF(MONTH(tb_bayar_kost.periode_bayar)=3,'Maret',
    IF(MONTH(tb_bayar_kost.periode_bayar)=4,'April',
    IF(MONTH(tb_bayar_kost.periode_bayar)=5,'Mei',
    IF(MONTH(tb_bayar_kost.periode_bayar)=6,'Juni',
    IF(MONTH(tb_bayar_kost.periode_bayar)=7,'Juli',
    IF(MONTH(tb_bayar_kost.periode_bayar)=8,'Agustus',
    IF(MONTH(tb_bayar_kost.periode_bayar)=9,'September',
    IF(MONTH(tb_bayar_kost.periode_bayar)=10,'Oktober',
    IF(MONTH(tb_bayar_kost.periode_bayar)=11,'November',
    IF(MONTH(tb_bayar_kost.periode_bayar)=12,'Desember','')))))))))))),
    ' s.d ',
    MONTH(DATE_ADD((SELECT tb_bayar_kost.tanggal_pembayaran FROM tb_bayar_kost WHERE tb_bayar_kost.kode_sewa = (SELECT tb_sewa_kamar.kode_sewa FROM tb_sewa_kamar WHERE tb_sewa_kamar.kode_kamar = '$kodekamar' AND tb_sewa_kamar.tanggal_selesai IS NULL LIMIT 0,1) ORDER BY tb_bayar_kost.kode_bayar DESC LIMIT 0,1), INTERVAL $periodebayar MONTH))),
    
    CONCAT(DATE_FORMAT((SELECT tb_bayar_kost.tanggal_pembayaran FROM tb_bayar_kost WHERE tb_bayar_kost.kode_sewa = (SELECT tb_sewa_kamar.kode_sewa FROM tb_sewa_kamar WHERE tb_sewa_kamar.kode_kamar = '$kodekamar' AND tb_sewa_kamar.tanggal_selesai IS NULL LIMIT 0,1) ORDER BY tb_bayar_kost.kode_bayar DESC LIMIT 0,1),'%d/%m/%Y')
           
    ,' s.d. ',
    DATE_FORMAT(DATE_ADD((SELECT tb_bayar_kost.tanggal_pembayaran FROM tb_bayar_kost WHERE tb_bayar_kost.kode_sewa = (SELECT tb_sewa_kamar.kode_sewa FROM tb_sewa_kamar WHERE tb_sewa_kamar.kode_kamar = '$kodekamar' AND tb_sewa_kamar.tanggal_selesai IS NULL LIMIT 0,1) ORDER BY tb_bayar_kost.kode_bayar DESC LIMIT 0,1), INTERVAL $periodebayar MONTH),'%d/%m/%Y')
    )    
    ) AS periodebayarkuitansi,
    
    (SELECT (tb_bayar_kost.total_harga - (tb_bayar_kost.total_bayar + tb_bayar_kost.total_bayar_sebelumnya)) FROM tb_bayar_kost WHERE tb_bayar_kost.kode_sewa = (SELECT tb_sewa_kamar.kode_sewa FROM tb_sewa_kamar WHERE tb_sewa_kamar.kode_kamar = '$kodekamar' AND tb_sewa_kamar.tanggal_selesai IS NULL LIMIT 0,1) ORDER BY tb_bayar_kost.kode_bayar DESC LIMIT 0,1) AS sisabayarsaatini,
    
    tb_bayar_kost.tanggal_bayar, 
    tb_kost.nama_kost, 
    tb_kost.logo, 
    tbv_status_bayar.status_bayar
    
    FROM tb_sewa_kamar 
    
    INNER JOIN tb_penyewa_kamar ON tb_sewa_kamar.kode_sewa = tb_penyewa_kamar.kode_sewa
    INNER JOIN tb_penyewa ON tb_penyewa_kamar.kode_penyewa = tb_penyewa.kode
    INNER JOIN tb_bayar_kost ON tb_penyewa_kamar.kode_sewa = tb_bayar_kost.kode_sewa
    INNER JOIN tb_kost ON tb_penyewa.kode_kost = tb_kost.kode_kost
    INNER JOIN tbv_status_bayar ON tb_sewa_kamar.kode_sewa = tbv_status_bayar.kode_sewa
    
    WHERE tb_sewa_kamar.tanggal_selesai IS NULL
    AND tb_bayar_kost.kode_bayar IN (SELECT max(tb_bayar_kost.kode_bayar) FROM tb_bayar_kost
    INNER JOIN tb_sewa_kamar ON tb_bayar_kost.kode_sewa = tb_sewa_kamar.kode_sewa
                                             WHERE tb_sewa_kamar.kode_kamar = '$kodekamar' 
                                             AND tb_sewa_kamar.tanggal_selesai IS NULL
                                             GROUP BY tb_sewa_kamar.kode_sewa)
                                             AND tb_sewa_kamar.kode_kamar = '$kodekamar' AND
                                             tb_penyewa.kode_kost = '$kodekost'
                                             GROUP BY tb_penyewa_kamar.kode_penyewa";
  $resultGetEmail = mysqli_query($koneksi, $queryGetEmailPenyewa);
    $namaList = array();
    $emailList = array();
    $nokw = "";
    $uang = "";
    $periodkwitansi = "";
    $period = "";
    $denda = "";
    $diskon = "";
    $totalbayar = "";
    $totalharga = "";
    $totalbayarsebelumnya = "";
    $sisabayar = "";
    $hargaperbulan = "";
    $metode = "";
    $tgls = "";
    $logo = "";
    $namakost = "";
    $status = "";
  if($resultGetEmail)
  {
    while($rows = mysqli_fetch_assoc($resultGetEmail))
    {
        array_push($namaList, $rows['nama']);
        array_push($emailList, $rows['email']);
        $nokw = $rows['kode_bayar'];
        $uang = $rows['total_bayar'];
        // $period = $periodebayar;
        $periodkwitansi = $rows['periodebayarkuitansi'];    // tambahan
        $period = $rows['periode_bayar'];
        $denda = $rows['denda'];
        $diskon = $rows['diskon'];
        $pindah = $rows['harga_pindah'];
        $totalbayar = $rows['total_bayar'];
        $totalharga = $rows['total_harga'];
        $totalbayarsebelumnya = $rows['total_bayar_sebelumnya'];
        $sisabayarsebelumnya = $rows['sisa_bayar_sebelumnya'];
        $metode = $rows['metode'];
        $sisabayar = (int)$totalharga - (int)$totalbayar;
        if($sisabayar < 0)
        {
            $sisabayar = 0;
        }
        $hargaperbulan = $rows['harga_perbulan'];
        $sisasaatini = $rows['sisabayarsaatini']; // tambahan
        $tgls = $rows['tanggal_bayar'];
        $logo = "https://pondok-huda.com" . $rows['logo'];
        $namakost = $rows['nama_kost'];
        if($rows['status_bayar'] == "Lunas")
        {
            $status = "Lunas";
        }
        else
        {
            $status = "Belum Lunas";
        }
    }
  }
  else {
      echo "Query Error " . mysqli_error($koneksi);
  }
  
    for($i = 0; count($namaList) > $i; $i++)
    {
        if($nama == "")
        {
            $nama = $namaList[$i];
        }
        else
        {
            $nama .= ", " . $namaList[$i];
            
        }
    }
    $invoiceFile = null;
    if($c == true)
    {  
        if($logo == "https://pondok-huda.com") {
            $logo = "https://pondok-huda.com/Assets/images/logo/default-logo-black.png";
        }
        require_once ph_receipt_asset('tcpdf');
        $pdf = new TCPDF();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // set margins
        $pdf->SetMargins(0, 0, 0, true);
        
        // set auto page breaks false
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->AddPage('P', 'A4');
        $img_file = ph_receipt_asset('template');
        // Display image on full page
        $pdf->Image($img_file, 0, 0, 210, 297, 'JPG', '', '', true, 200, '', false, false, 0, false, false, true);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(0);
        $pdf->SetFooterMargin(0);
        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        
        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        // $pdf->Image($img_file, 0, 0, 210, 297,
        
        if($totalbayarsebelumnya > 0)
        {
            $html = '
                <html>
                    <head>
                    </head>
                    <body>
                        <table width="100%" cellpadding="0" cellspacing="0" border="1">
                            <tr>
                                <td height="180px" colspan="6"></td>
                            </tr>
                            <tr>
                                <td width="10%"></td>
                                <td colspan="2" width="40%">
                                        <img src="'.$logo.'" width="60px" height="60px"><br>
                                        '.$namakost.'
                                </td>
                                <td colspan="3" width="50%" align="right">No. '.$nokw.'</td>
                            </tr>
                            <tr>
                                <td colspan="6" height="85px"></td>
                            </tr>
                            <tr>
                                <td colspan="3" width="50%">
                                    <table border="1" width="100%">
                                        <tr>
                                            <td width="5%"></td>
                                            <td width="50%">
                                                Detail Pembayaran       
                                            </td>
                                            <td width="45%" align="right">
                                                '.$metode.'
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" height="25px"></td>
                                        </tr>
                                        <tr>
                                            <td width="5%"></td>
                                            <td width="50%">
                                                Detail Bayar       
                                            </td>
                                            <td width="45%" align="right">
                                                Jumlah
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" height="25px"></td>
                                        </tr>
                                        <tr>
                                            <td width="5%"></td>
                                            <td width="50%">
                                                Sisa Pembayaran sebelum
                                            </td>
                                            <td width="45%" align="right">
                                                '.$sisabayarsebelumnya.'
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" height="25px"></td>
                                        </tr>
                                        <tr>
                                            <td width="5%"></td>
                                            <td width="50%">
                                                Total Bayar       
                                            </td>
                                            <td width="45%" align="right">
                                                '.$totalbayar.'
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" height="25px"></td>
                                        </tr>
                                        <tr>
                                            <td width="5%"></td>
                                            <td width="50%">
                                                Sisa Pembayaran       
                                            </td>
                                            <td width="45%" align="right">
                                                '.$sisasaatini.'
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td colspan="3" width="50%">
                                <table border="1" width="100%">
                                        <tr>
                                            <td width="10%"></td>
                                            <td width="90%">
                                                Untuk Pembayaran Sewa    
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" height="25px"></td>
                                        </tr>
                                        <tr>
                                            <td width="10%"></td>
                                            <td width="90%">
                                                Periode :
                                                <br>
                                                '.$periodkwitansi.'
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" height="25px"></td>
                                        </tr>
                                        <tr>
                                            <td width="10%"></td>
                                            <td width="90%">
                                                Status Bayar :
                                                <br>
                                                '.$status.'
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </body>
                </html>';
        }
        else
        {
            $html = '
                <html>
                    <head>
                    </head>
                    <body>
                        <table width="100%" cellpadding="0" cellspacing="0" border="1">
                            <tr>
                                <td height="180px" colspan="6"></td>
                            </tr>
                            <tr>
                                <td width="10%"></td>
                                <td colspan="2" width="40%">
                                        <img src="'.$logo.'" width="60px" height="60px"><br>
                                        '.$namakost.'
                                </td>
                                <td colspan="3" width="50%" align="right">No. '.$nokw.'</td>
                            </tr>
                            <tr>
                                <td colspan="6" height="85px"></td>
                            </tr>
                            <tr>
                                <td colspan="3" width="50%">
                                    <table border="1" width="100%">
                                        <tr>
                                            <td width="5%"></td>
                                            <td width="50%">
                                                Detail Pembayaran       
                                            </td>
                                            <td width="45%" align="right">
                                                '.$metode.'
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" height="25px"></td>
                                        </tr>
                                        <tr>
                                            <td width="5%"></td>
                                            <td width="50%">
                                                Detail Bayar       
                                            </td>
                                            <td width="45%" align="right">
                                                Jumlah
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" height="25px"></td>
                                        </tr>
                                        <tr>
                                            <td width="5%"></td>
                                            <td width="50%">
                                                Harga Kamar x Periode       
                                            </td>
                                            <td width="45%" align="right">
                                                '.$hargaperbulan.' x '.$period.'
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" height="25px"></td>
                                        </tr>
                                        <tr>
                                            <td width="5%"></td>
                                            <td width="50%">
                                                Harga Pindah       
                                            </td>
                                            <td width="45%" align="right">
                                                '.$pindah.'
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" height="25px"></td>
                                        </tr>
                                        <tr>
                                            <td width="5%"></td>
                                            <td width="50%">
                                                Denda       
                                            </td>
                                            <td width="45%" align="right">
                                                '.$denda.'
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" height="25px"></td>
                                        </tr>
                                        <tr>
                                            <td width="5%"></td>
                                            <td width="50%">
                                                Diskon       
                                            </td>
                                            <td width="45%" align="right">
                                                '.$diskon.'
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" height="70px"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" align="right">Total Harga '.$totalharga.'</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" height="25px"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" align="right">Dibayarkan '.$totalbayar.'</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" height="25px"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" align="right">Sisa Pembayaran '.$sisabayar.'</td>
                                        </tr>
                                    </table>
                                </td>
                                <td colspan="3" width="50%">
                                <table border="1" width="100%">
                                        <tr>
                                            <td width="10%"></td>
                                            <td width="90%">
                                                Untuk Pembayaran Sewa    
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" height="25px"></td>
                                        </tr>
                                        <tr>
                                            <td width="10%"></td>
                                            <td width="90%">
                                                Periode :
                                                <br>
                                                '.$periodkwitansi.'
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" height="25px"></td>
                                        </tr>
                                        <tr>
                                            <td width="10%"></td>
                                            <td width="90%">
                                                Status Bayar :
                                                <br>
                                                '.$status.'
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </body>
                </html>';
        }
                
        $pdf->writeHTML($html, true, false, true, false, '');
        
        // $pdf->Output();
        
        // Save PDF to server
        $invoiceFile = ph_invoice_temp_file();
        $pdf->Output($invoiceFile, 'F');
        $c = false;
    }
    // ---------------------------SEND EMAIL--------------------------------
    //recipient
    for($i = 0; count($emailList) > $i; $i++)
    {
        $to = $emailList[$i];
    
        //sender
        $from = ph_mail_from();
        $fromName = ph_mail_from_name();
        
        //email subject
        $subject = 'Pembayaran Sewa Kost'; 
        
        //attachment file path
        $file = $invoiceFile;
        
        //email body content
        $htmlContent = '<h1>Pembayaran Sewa Kost Berhasil</h1>
            <p>Jika tidak menerima kwitansi/invoice silahkan hubungi admin.</p>';
        
        //header for sender info
        $header = '';
        $headers = "From: $fromName"." <".$from.">";
        
        
        //boundary 
        $semi_rand = md5(time()); 
        $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
        
        //headers for attachment 
        $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 
        
        //multipart boundary 
        $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" .
        "Content-Transfer-Encoding: 7bit\n\n" . $htmlContent . "\n\n"; 
        
        //preparing attachment
        if(!empty($file) > 0){
            if(is_file($file)){
                $message .= "--{$mime_boundary}\n";
                $fp =    @fopen($file,"rb");
                $data =  @fread($fp,filesize($file));
        
                @fclose($fp);
                $data = chunk_split(base64_encode($data));
                $message .= "Content-Type: application/octet-stream; name=\"".basename($file)."\"\n" . 
                "Content-Description: ".basename($file)."\n" .
                "Content-Disposition: attachment;\n" . " filename=\"".basename($file)."\"; size=".filesize($file).";\n" . 
                "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
            }
        }
        $message .= "--{$mime_boundary}--";
        $returnpath = "-f" . $from;
        
        //send email
        $mail = is_string($file) && is_file($file) && ph_send_mail($to, $subject, $message, $headers);
        
        //email sending status
        // echo $mail?"<h1>Mail sent. ".$cekemail."</h1>":"<h1>Mail sending failed.</h1>";
    }
        if (isset($invoiceFile) && is_file($invoiceFile)) {
            @unlink($invoiceFile);
        }
        $penyewapindah = 'penyewa berhasil pindah'; // 3.1
    }
    else
    {
      $penyewapindah = 'data laporan keuangan (tunai, tepat waktu, 1 bulan) gagal diinput, Error: '. mysqli_error($koneksi);
    }
}

/*if($pembayaran)
{
    // 2.1 bayar sebulan
    if ($periodebayar == '1')
    {
        $queryinsertlaporankeu = "INSERT INTO tb_keu_jurnal_umum
                            (kode_transaksi,
                            kode_akun, tanggal, keterangan,
                            posisi, jumlah)
                            VALUES(CONCAT('$kodebayar', '-1'),
                            '1-1100', '$tanggalMulai', 'Kas',
                            'Debit', '$totalbayar'),
                            (CONCAT('$kodebayar', '-1'),
                            '5-1100', '$tanggalMulai',
                            CONCAT('Pendapatan Sewa Kamar ', '$nokamar'),
                            'Kredit', $totalbayar + $diskon - $hargapindah)";

        $resultinsertlaporankeu = mysqli_query($koneksi, $queryinsertlaporankeu);
        
        if($resultinsertlaporankeu)
        {
            //kalo ada diskon
            if ($diskon != '0')
            {
                $queryinsertlaporankeu = "INSERT INTO tb_keu_jurnal_umum
                (kode_transaksi, kode_akun, tanggal, keterangan, posisi, jumlah)
                VALUES(CONCAT('$kode_pembayaran', '-1'), '1-0100', '$tanggal_bayar', 'Diskon', 'Debit', $diskon)";
                
                $resultinsertlaporankeu = mysqli_query($koneksi, $queryinsertlaporankeu);
                
                if($resultinsertlaporankeu)
                {
                    if($hargapindah != 0)
                    {
                        $queryinsertlaporankeu = "INSERT INTO tb_keu_jurnal_umum
                        (kode_transaksi, kode_akun, tanggal, keterangan, posisi, jumlah)
                        VALUES(CONCAT('$kode_pembayaran', '-1'), '5-2200', '$tanggal_bayar', 'Pendapatan Pindah Kamar', 'Kredit', $hargapindah)";
                        
                        $resultinsertlaporankeu = mysqli_query($koneksi, $queryinsertlaporankeu);
                        
                        if($resultinsertlaporankeu)
                        {
                            $penyewapindah = 'penyewa berhasil pindah'; // 3.1
                        }
                        else
                        {
                            $penyewapindah = 'data laporan keuangan (harga pindah) gagal diinput, Error: '. mysqli_error($koneksi);
                        }
                    }
                    else
                    {
                        $penyewapindah = 'penyewa berhasil pindah'; // 3.1
                    }
                }
                else
                {
                    $penyewapindah = 'data laporan keuangan (diskon) gagal diinput, Error: '. mysqli_error($koneksi);
                }
            }
            else
            {
                $penyewapindah = 'penyewa berhasil pindah'; // 3.1
            }
        }
        else
        {
          $penyewapindah = 'data laporan keuangan (tunai, tepat waktu, 1 bulan) gagal diinput, Error: '. mysqli_error($koneksi);
        }
    }
    // 2.2 bayar lebih dari sebulan
    else
    {
        $queryinsertlaporankeu = "INSERT INTO tb_keu_jurnal_umum
                            (kode_transaksi,
                            kode_akun, tanggal, keterangan,
                            posisi, jumlah)
                            VALUES(CONCAT('$kodebayar', '-3'),
                            '1-1100', '$tanggalMulai', 'Kas',
                            'Debit', $totalbayar),
                            (CONCAT('$kodebayar', '-3'),
                            '1-1400', '$tanggalMulai',
                            CONCAT('Sewa Diterima Di Muka Kamar ', $nokamar),
                            'Kredit', $totalbayar + $diskon - $hargapindah)";
  
        $resultinsertlaporankeu = mysqli_query($koneksi, $queryinsertlaporankeu);
        
        if($resultinsertlaporankeu)
        {
            //kalo ada diskon
            if ($diskon != '0')
            {
                $queryinsertlaporankeu = "INSERT INTO tb_keu_jurnal_umum
                (kode_transaksi, kode_akun, tanggal, keterangan, posisi, jumlah)
                VALUES(CONCAT('$kode_pembayaran', '-1'), '1-0100', '$tanggal_bayar', 'Diskon', 'Debit', $diskon)";
                
                $resultinsertlaporankeu = mysqli_query($koneksi, $queryinsertlaporankeu);
                
                if($resultinsertlaporankeu)
                {
                    if($hargapindah != 0)
                    {
                        $queryinsertlaporankeu = "INSERT INTO tb_keu_jurnal_umum
                        (kode_transaksi, kode_akun, tanggal, keterangan, posisi, jumlah)
                        VALUES(CONCAT('$kode_pembayaran', '-1'), '5-2200', '$tanggal_bayar', 'Pendapatan Pindah Kamar', 'Kredit', $hargapindah)";
                        
                        $resultinsertlaporankeu = mysqli_query($koneksi, $queryinsertlaporankeu);
                        
                        if($resultinsertlaporankeu)
                        {
                            $penyewapindah = 'penyewa berhasil pindah'; // 3.1
                        }
                        else
                        {
                            $penyewapindah = 'data laporan keuangan (harga pindah) gagal diinput, Error: '. mysqli_error($koneksi);
                        }
                    }
                    else
                    {
                        $penyewapindah = 'penyewa berhasil pindah'; // 3.1
                    }
                }
                else
                {
                    $penyewapindah = 'data laporan keuangan (diskon) gagal diinput, Error: '. mysqli_error($koneksi);
                }
            }
            else
            {
                $penyewapindah = 'penyewa berhasil pindah'; // 3.1
            }
        }
        else
        {
          $penyewapindah = 'data laporan keuangan (tunai, tepat waktu, sddm) gagal diinput, Error: '. mysqli_error($koneksi);
        }
    }
}*/
if (count($kodepenyewa) == 1)
{
    foreach ($namapenyewa as $penyewa)
    {
        if (isset($penyewa))
        {
            $penyewaberes = $penyewa;
        }
    }
    
    if(isset($kodeadmin))
    {
        $log = CreateLogAdmin($koneksi, $namaadmin.' memindahkan penyewa di kamar '.$nokamarlama.' menuju kamar '.$nokamar.' atas nama penyewa '.$penyewaberes, $kodeadmin, $kodekost);
    }
    
    if(isset($kodeowner))
    {
        $log = CreateLogOwner($koneksi, $namaowner.' memindahkan penyewa di kamar '.$nokamarlama.' menuju kamar '.$nokamar.' atas nama penyewa '.$penyewaberes, $kodeowner, $kodekost);
    }
}
else
{
    if(isset($kodeadmin))
    {
        $log = CreateLogAdmin($koneksi, $namaadmin.' memindahkan penyewa di kamar '.$nokamarlama.' menuju kamar '.$nokamar.' atas nama penyewa '.$namapenyewa[0].', '.$namapenyewa[1], $kodeadmin, $kodekost);
    }
    
    if(isset($kodeowner))
    {
        $log = CreateLogOwner($koneksi, $namaowner.' memindahkan penyewa di kamar '.$nokamarlama.' menuju kamar '.$nokamar.' atas nama penyewa '.$namapenyewa[0].', '.$namapenyewa[1], $kodeowner, $kodekost);
    }
}
echo json_encode(array('datapindah' => $penyewapindah));

// -------------------------------------------------

function GenerateKodePembayaran($kode, $nokamar, $kodekost)
{
    // kode = KW-thn/bln/nokamar/nourut (no urut = 6 digit)
    $noUrutTerakhir = substr($kode, -6);
    
    $noUrutBaru =  $noUrutTerakhir + 1;
    
    $hasil = 'KW-'.date('Y/m/').$nokamar.'/'.$kodekost.'/'.substr($noUrutTerakhir, 0, -1 * abs(strlen($noUrutBaru))).$noUrutBaru;
    
    return $hasil;
}
?>
