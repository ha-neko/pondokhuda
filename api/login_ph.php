<?php

include ('kon.php');
include ("admin_log.php");
include ("owner_log.php");

ph_rate_limit('login', 30, 900, 60);

$kode = $_POST['kode'];
$pin = $_POST['pin'];

// $kode = "000001";
// $pin = '123g456';

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if( !$koneksi)
{
	return;
}

// $lemparJSON = json_encode(array('personalinfo' => 'tidak ada data'));
$lemparJSON = "";

// echo "depan: " . substr($kode, 0, 1) . "<br>";
// echo "belakang: " . substr($kode, strlen($kode) - 1, 1) . "<br>";
// echo "len: " . strlen($kode) . "<br>";
// cek ada data ato gk
if( $kode != "" && $pin != "")
{
    if( substr($kode, 0, 1) == "o" && substr($kode, strlen($kode) - 1, 1) == "w" && strlen($kode) == 5)
    {
        // echo "ini owner";
        $query = "SELECT * FROM tb_owner WHERE kode = '$kode' AND pin = '$pin'";
        $result = mysqli_query($koneksi, $query);
        $json = array();
        
        if( mysqli_num_rows($result) == 1)
        {
            
            while($rows = mysqli_fetch_assoc($result))
            {
                $json[] = array(
                    "nama" => $rows['nama'],
                    "kode" => $rows['kode']
                );
                $nama = $rows['nama'];
                $kodeowner = $rows['kode'];
            }
            $log = CreateLogOwner($koneksi, $nama.' melakukan login di aplikasi.', $kodeowner, NULL);
            $lemparJSON = json_encode(array('personalinfoowner' => $json, 'log' => $log));
        }
    }
    else if( substr($kode, 0, 1) == "a" && substr($kode, strlen($kode) - 1, 1) == "d" && strlen($kode) == 5)
    {
        // echo "ini admin";
        $query = "SELECT * FROM tb_admin WHERE kode = '$kode' AND pin = '$pin'";
        $result = mysqli_query($koneksi, $query);
        $json = array();
        
        if( mysqli_num_rows($result) == 1)
        {
            
            while($rows = mysqli_fetch_assoc($result))
            {
                $json[] = array(
                    "nama" => $rows['nama'],
                    "kode" => $rows['kode']
                    );
                $nama = $rows['nama'];
                $kodeadmin = $rows['kode'];
            }
            $log = CreateLogAdmin($koneksi, $nama.' melakukan login di aplikasi.', $kodeadmin, NULL);
            $lemparJSON = json_encode(array('personalinfoadmin' => $json));
        }
    }
    else if(substr($kode, 0, 2) == "so")
    {
        $query = "SELECT * FROM tb_super_owner WHERE kode = '$kode' AND pin = '$pin'";
        $result = mysqli_query($koneksi, $query);
        $json = array();
        
        if( mysqli_num_rows($result) == 1)
        {
            
            while($rows = mysqli_fetch_assoc($result))
            {
                $json[] = array(
                    "id" => $rows['id'],
                    "nama" => $rows['nama'],
                    "kode" => $rows['kode']
                    );
            }
            $lemparJSON = json_encode(array('personalinfosuperowner' => $json));
        }
    }
    // else if((substr($kode, 0, 1) == "p" || (substr($kode, 0, 1) == "u")) && ((substr($kode, strlen($kode) - 1, 1) == "a") || (substr($kode, strlen($kode) - 1, 1) == "b")) && strlen($kode) == 6)
    else if((substr($kode, 0, 1) == "p" || substr($kode, 0, 1) == "u") && 
            (substr($kode, 1, 1) == "a" || substr($kode, 1, 1) == "b"))
    {
        // echo "ini penyewa";
        
        
        // 'datapembayaran' => $datapembayaran, 'tglbayar' => $tglbayar, 'nexttglbayar' => $nexttglbayar, 'duedatebayar' => $sisaharibayar, 'statusbayar' => $statusbayar, 'hutang' => $sisabayarsebelum, 'terbayar' => $bayarsebelumnya
        // ---------------------------------------------- end of data bayar

        // get data diri client (gabungin data pembayaran ke satu json)
        $queryBiodata = "SELECT
                        tb_penyewa.kode,
                        tb_penyewa.noktp,
                        tb_kamar.nokamar AS nokamar,
                        tb_penyewa.nama,
                        tb_penyewa.tgllahir,
                        tb_penyewa.jk,
                        tb_penyewa.nohp,
                        tb_penyewa.email,
                        tb_penyewa.urlfoto,
                        tb_penyewa.namaortu,
                        tb_penyewa.nohportu,
                        tb_penyewa.alamat,
                        tb_penyewa.kelurahan,
                        tb_penyewa.kecamatan,
                        tb_penyewa.kotakab,
                        tb_penyewa.kodepos,
                        tb_penyewa.tempatkuliahkerja,
                        tb_penyewa.jurusankuliah,
                        sewa_kamar.periode_bayar AS periodebayar,
                        tb_penyewa.status,
                        tb_penyewa.nomorpin,
                        tb_kost.nama_kost,
                        tb_kost.alamat AS alamatkost,
                        tb_kost.emailkost,
                        tb_kost.primarycolor,
                        tb_kost.logo
                        
                        FROM tb_penyewa
                        
                        INNER JOIN tb_penyewa_kamar ON tb_penyewa_kamar.kode_penyewa = tb_penyewa.kode
                        INNER JOIN (SELECT * FROM tb_sewa_kamar WHERE tanggal_selesai IS NULL) sewa_kamar ON sewa_kamar.kode_sewa = tb_penyewa_kamar.kode_sewa
                        INNER JOIN tb_kamar ON tb_kamar.id = sewa_kamar.kode_kamar
                        INNER JOIN tb_kost ON tb_penyewa.kode_kost = tb_kost.kode_kost
                        
                        WHERE kode = '$kode' AND nomorpin = '$pin'";
        
        // echo "kode: " . $kode . ", pin: " . $pin . "\n";
        $result = mysqli_query($koneksi, $queryBiodata);
        $jsonfinal = array();
        
        if( mysqli_num_rows($result) == 1)
        {
            while($rows = mysqli_fetch_assoc($result))
            {
                $jsonfinal[] = array(
                    "kode" => $rows['kode'],
                    "nomorktp" => $rows['noktp'],
                    "nomorkamar" => $rows['nokamar'],
                    "nama" => $rows['nama'],
                    "tgllahir" => $rows['tgllahir'],
                    "jeniskelamin" => $rows['jk'],
                    "nomorhp" => $rows['nohp'],
                    "email" => $rows['email'],
                    "urlfoto" => "https://pondok-huda.com" . $rows['urlfoto'],
                    "namaortu" => $rows['namaortu'],
                    "nomorhportu" => $rows['nohportu'],
                    "alamatrumah" => $rows['alamat'],
                    "kelurahan" => $rows['kelurahan'],
                    "kecamatan" => $rows['kecamatan'],
                    "namakotakabupaten" => $rows['kotakab'],
                    "kodepos" => $rows['kodepos'],
                    "tempatkuliahkerja" => $rows['tempatkuliahkerja'],
                    "jurusankuliah" => $rows['jurusankuliah'],
                    "periodebayar" => $rows['periodebayar'],
                    "status" => $rows['status'],
                    "namakost" => $rows['nama_kost'],
                    "alamatkost" => $rows['alamatkost'],
                    "emailkost" => $rows['emailkost'],
                    "primary" => $rows['primarycolor'],
                    "logo" => "https://pondok-huda.com" . $rows['logo']
                    );
            }
            // ambil kontak admin untuk whatsapp
            $kontakadmin = array();
            $querykontakadmin = "SELECT
                    	tb_admin.nama,
                        tb_admin.nomor_telepon
                    FROM
                    	tb_penyewa
                    INNER JOIN tb_admin_kost ON tb_admin_kost.kode_kost = tb_penyewa.kode_kost
                    INNER JOIN tb_admin ON tb_admin.kode = tb_admin_kost.kode_admin
                    WHERE 
                    	tb_penyewa.kode = '$kode' AND tb_penyewa.nomorpin = '$pin'";
            $resultkontakadmin = mysqli_query($koneksi, $querykontakadmin);
            if( mysqli_num_rows($resultkontakadmin))
            {
                while($rowskontakadmin = mysqli_fetch_assoc($resultkontakadmin))
                {
                    $kontakadmin[] = array(
                        "namaadmin" => $rowskontakadmin['nama'],
                        "wasap" => $rowskontakadmin['nomor_telepon']
                        );
                }
            }
            // else
            // {
            //     $kontakadmin = null;
            // }
            
            // get data pembayaran client
            $finalDataPembayaran = array();
            // ---------------------
            $datapembayaran = array();
            // get data semua penyewa -----------------------------------------
            // $querygetpembayaran = "SELECT kode_bayar, tb_kamar.nokamar,
    //                     tb_bayar_kost.tanggal_pembayaran, tb_bayar_kost.tanggal_bayar,
    //                     tb_bayar_kost.periode_bayar, tb_bayar_kost.harga_perbulan,
    //                     date_add(tbv_status_bayar.tanggal_pembayaran_selanjutnya, INTERVAL 3 day) AS duedatedenda,
    //                     tbv_status_bayar.denda, diskon, total_harga, total_bayar, metode
				// 		FROM tb_bayar_kost
    //                     INNER JOIN tb_sewa_kamar ON tb_sewa_kamar.kode_sewa=tb_bayar_kost.kode_sewa
    //                     INNER JOIN tb_kamar ON tb_sewa_kamar.kode_kamar=tb_kamar.id
    //                     INNER JOIN tb_penyewa_kamar ON tb_sewa_kamar.kode_sewa=tb_penyewa_kamar.kode_sewa
    //                     INNER JOIN tb_penyewa ON tb_penyewa_kamar.kode_penyewa=tb_penyewa.kode
    //                     INNER JOIN tbv_status_bayar ON tbv_status_bayar.kode_sewa = tb_penyewa_kamar.kode_sewa
    //                     WHERE tb_penyewa.kode='ub0005' AND nomorpin='05'";
            
    //         $querygetpembayaran = "SELECT kode_bayar, 
    //                     tb_kamar.nokamar,
    //                     date_sub(tb_bayar_kost.tanggal_pembayaran, INTERVAL 3 day) AS reminderdate,
    //                     tb_bayar_kost.tanggal_pembayaran, 
    //                     tb_bayar_kost.tanggal_bayar,
    //                     tb_bayar_kost.periode_bayar, 
    //                     tb_bayar_kost.harga_perbulan,
    //                     date_add(tb_bayar_kost.tanggal_pembayaran, INTERVAL 33 day) AS duedatedenda,
    //                     tb_bayar_kost.denda, 
    //                     diskon, total_harga, total_bayar, metode,
    //                 CONCAT(tb_bayar_kost.tanggal_pembayaran,' s.d.' , DATE_ADD(tb_bayar_kost.tanggal_pembayaran, INTERVAL 1 MONTH)) AS periodebulanbayar,
    //                 (tb_bayar_kost.total_harga - (tb_bayar_kost.total_bayar + tb_bayar_kost.total_bayar_sebelumnya)) AS sisabayarsaatitu,
    //                 IF((tb_bayar_kost.total_harga - (tb_bayar_kost.total_bayar + tb_bayar_kost.total_bayar_sebelumnya)) <= 0,'Lunas','Belum Lunas') AS statuspembayaran
                    
				// 		FROM tb_bayar_kost
    //                     INNER JOIN tb_sewa_kamar ON tb_sewa_kamar.kode_sewa=tb_bayar_kost.kode_sewa
    //                     INNER JOIN tb_kamar ON tb_sewa_kamar.kode_kamar=tb_kamar.id
    //                     INNER JOIN tb_penyewa_kamar ON tb_sewa_kamar.kode_sewa=tb_penyewa_kamar.kode_sewa
    //                     INNER JOIN tb_penyewa ON tb_penyewa_kamar.kode_penyewa=tb_penyewa.kode
    //                     WHERE tb_penyewa.kode='$kode' AND nomorpin='$pin' ORDER BY tb_bayar_kost.tanggal_bayar ASC";
    
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
            WHERE tb_penyewa.kode='$kode' AND nomorpin='$pin' ORDER BY tb_bayar_kost.kode_bayar DESC, tb_bayar_kost.tanggal_bayar DESC";
            
            
            $resultgetpembayaran = mysqli_query($koneksi, $querygetpembayaran);
            if( mysqli_num_rows($resultgetpembayaran) > 0)
            {
                while($rows = mysqli_fetch_assoc($resultgetpembayaran))
                {
                    $datapembayaran[] = array(
                        
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
            
            // $querygetnextbayar = "SELECT tbv_status_bayar.tanggal_bayar_sebelumnya, 
            // IF( status_bayar = 'Belum Lunas', sisa_bayar_sebelumnya, harga_perbulan + denda) AS abc,           
            // tbv_status_bayar.tanggal_pembayaran_selanjutnya, tbv_status_bayar.hari_menuju_bayar, tbv_status_bayar.status_bayar,tbv_status_bayar.total_harga_sebelumnya, 
            // tbv_status_bayar.sisa_bayar_sebelumnya, tbv_status_bayar.total_bayar_sebelumnya,
            // tbv_status_bayar.tanggal_alarm AS Alarm,
            
            // IF(tbv_status_bayar.sisa_bayar_sebelumnya = 0, CONCAT(tbv_status_bayar.tanggal_pembayaran_sebelumnya,' s.d. ', DATE_ADD(tbv_status_bayar.tanggal_pembayaran_sebelumnya, INTERVAL tbv_status_bayar.periode_bayar_sebelumnya MONTH)), CONCAT(tbv_status_bayar.tanggal_pembayaran_selanjutnya,' s.d. ', DATE_ADD(tbv_status_bayar.tanggal_pembayaran_selanjutnya, INTERVAL tbv_status_bayar.periode_bayar_sebelumnya MONTH))) AS resumetagihan,
            
            // CONCAT(tbv_status_bayar.tanggal_pembayaran_selanjutnya, ' s.d. ', DATE_ADD(tbv_status_bayar.tanggal_pembayaran_selanjutnya, INTERVAL tbv_status_bayar.periode_bayar_sebelumnya MONTH)) AS periodebayarbulanselanjutnya
            
            // FROM tbv_status_bayar
            // INNER JOIN tb_penyewa_kamar ON tb_penyewa_kamar.kode_sewa = tbv_status_bayar.kode_sewa
            // WHERE tb_penyewa_kamar.kode_penyewa = '$kode'";
            
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
                    WHERE tb_penyewa_kamar.kode_penyewa = '$kode' AND tb_penyewa.nomorpin = '$pin'";
            
            $resultgetnextbayar = mysqli_query($koneksi, $querygetnextbayar);
            if( mysqli_num_rows($resultgetnextbayar) == 1)
            {
                while($rows = mysqli_fetch_assoc($resultgetnextbayar))
                {
                    $finalDataPembayaran = array(
                        "kumulasi" => $rows['abc'],
                        "tanggalbayar" => $rows['tanggal_bayar_sebelumnya'],
                        "nexttglbayar" => $rows['tanggal_pembayaran_selanjutnya'],
                        "sisaharibayar" => $rows['jatuhtempobulandepan'],
                        "statusbayar" => $rows['status_bayar'],
                        "tagihantotal" => "Rp " . $rows['total_harga_sebelumnya'],
                        "sisabayarsebelumnya" => "Rp " . $rows['sisa_bayar_sebelumnya'],
            "bayarsebelumnya" => "Rp " . $rows['total_bayar_sebelumnya'],
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
                    
                $lemparJSON = json_encode(array('personalinfopenyewa' => $jsonfinal, 'personalinfobayar' => $finalDataPembayaran, 'resumepembayaran' => $keteranganResume, 'kontakadmin' =>$kontakadmin));
            }
            else
            {
                $finalDataPembayaran[] = null;
            }
        
        }
        else
        {
            // data ganda atau tidak ada
            $lemparJSON = json_encode(array('personalinfo' => 'data tidak ada pada sistem'));
        }
    }
    else 
    {
        $lemparJSON = json_encode(array('personalinfo' => 'tidak ada data'));
    }
}
else
{
    $lemparJSON = json_encode(array('personalinfo'=>'inputkurang'));
}

echo $lemparJSON;
?>