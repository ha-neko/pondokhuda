<?php
include('kon.php');
include ("admin_log.php");
include ("owner_log.php");

//$kode = $_POST['_kode'];
$noktp = $_POST['_noktp'];
$nama = $_POST['_nama'];
// $tgllahir = $_POST['_tgllahir'];

// $jk = $_POST['_jk'];
$nohp = $_POST['_nohp'];
$email = $_POST['_email'];
// $urlfoto = $_POST['_urlfoto'];
$foto = $_POST['_foto'];
// $namaortu = $_POST['_namaortu'];
// $nohportu = $_POST['_nohportu'];

// $alamat = $_POST['_alamat'];
// $kelurahan = $_POST['_kelurahan'];
// $kecamatan = $_POST['_kecamatan'];
// $kotakab = $_POST['_kotakab'];
// $provinsi = $_POST['_provinsi'];
// $kodepos = $_POST['_kodepos'];

// $tempatkuliahkerja = $_POST['_tempatkuliahkerja'];
// $jurkuliah = $_POST['_jurkuliah'];
$status = "on";
$pinNum = GetRandomPIN();

if(isset($_POST['_noktp2']))
{
    //$kode2 = $_POST['_kode2'];
    $noktp2 = $_POST['_noktp2'];
    $nama2 = $_POST['_nama2'];
    // $tgllahir2 = $_POST['_tgllahir2'];
    
    // $jk2 = $_POST['_jk2'];
    $nohp2 = $_POST['_nohp2'];
    $email2 = $_POST['_email2'];
    // $urlfoto2 = $_POST['_urlfoto2'];
    $foto2 = $_POST['_foto2'];
    // $namaortu2 = $_POST['_namaortu2'];
    // $nohportu2 = $_POST['_nohportu2'];
    
    // $alamat2 = $_POST['_alamat2'];
    // $kelurahan2 = $_POST['_kelurahan2'];
    // $kecamatan2 = $_POST['_kecamatan2'];
    // $kotakab2 = $_POST['_kotakab2'];
    // $provinsi2 = $_POST['_provinsi2'];
    // $kodepos2 = $_POST['_kodepos2'];
    
    // $tempatkuliahkerja2 = $_POST['_tempatkuliahkerja2'];
    // $jurkuliah2 = $_POST['_jurkuliah2'];
    $status2 = "on";
    $pinNum2 = GetRandomPIN();
    
    // Auto isi
    // $noktp2 = '0';
    $tgllahir2 = '0000-00-00';
    $jk2 = 'Wanita';
    $namaortu2 = '0';
    $nohportu2 = '0';
    $alamat2 = '0';
    $kelurahan2 = '0';
    $kecamatan2 = '0';
    $kotakab2 = '0';
    $provinsi2 = '0';
    $kodepos2 = '0';
    $tempatkuliahkerja2 = '0';
    $jurkuliah2 = '0';
}


$kodekamar = $_POST['_kodekamar'];
$nokamar = $_POST['_nokamar'];
$kamarterisi = $_POST['_kamarterisi'] ;
$hargakamar = $_POST['_hargakamar'] ;
$periodebayar = $_POST['_periodebayar'] ;
$diskon = $_POST['_diskon'] ;
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

// Auto isi
// $noktp = '0';
$tgllahir = '0000-00-00';
$jk = 'Wanita';
$namaortu = '0';
$nohportu = '0';
$alamat = '0';
$kelurahan = '0';
$kecamatan = '0';
$kotakab = '0';
$provinsi = '0';
$kodepos = '0';
$tempatkuliahkerja = '0';
$jurkuliah = '0';


$koneksi = mysqli_connect($host, $user, $pass, $daba);

if( !$koneksi)
{
	return;
}

// IMAGE PROPERTIES
// $HOME_SERVER = "/home/pondokhu/public_html";
$USER_IMAGE_FOLDER = "/Assets/images/user/";
// $target_directory = $HOME_SERVER . $USER_IMAGE_FOLDER;
// $filename = $_FILES['_foto']['name'];
// $filesize = $_FILES['_foto']['size'];
// $file_tmp = $_FILES['_foto']['tmp_name'];
// $filetype = $_FILES['_foto']['type'];
// $tmp = explode('.', $filename);
// $file_ext = strtolower(end($tmp));
// $target_file_to_move = $target_directory . $kode . "." . $file_ext;
// $uploadOK = 1;

// PROPERTIES
date_default_timezone_set("Asia/Bangkok");
$inputDate = date("Y-m-d H:i:s");
// --------------------------------------------------------------------

$messageERROR;
// cek if file is existed
// if( file_exists($target_file_to_move)){
//   $uploadOK = 0;
//   $messageERROR = "FILE EXIST";
// } 
// // cek if file limit
// if( $filesize > 1000000){
//   $uploadOK = 0;
//   $messageERROR = "FILE SIZE";
// } 
// // cek limit file type
// if( $filetype != "image/jpg" && $filetype != "image/png" && $filetype != "image/jpeg") {
//     $uploadOK = 0;
//     $messageERROR = "FILE TYPE";
// }

// get kode generate for user -----------------------------------------
$queryGetLastKodeInput = "SELECT kode FROM tb_penyewa
                          WHERE kode_kost='$kodekost'
                          ORDER BY time_created DESC LIMIT 0,1";
$resultGetLastKodeInput = mysqli_query($koneksi, $queryGetLastKodeInput);

if(mysqli_num_rows($resultGetLastKodeInput) > 0)
{
    while($rows = mysqli_fetch_assoc($resultGetLastKodeInput))
    {
        $lastKode = $rows['kode'];
    }
    
    $kode = GenerateNextCode($lastKode, $kodekost);
    // echo "\nnext: " . $nextkode;
}
else
{
    // random
    $rand1 = rand(0,70);
    $rand2 = rand(0,100);
    
    // get random 1, 2
    $kode = $rand1 < 45 ? "p" : "u";
    $kode = $rand2 < 60 ? $kode .= "a" : $kode .= "b";
    $kode .= '0001-'.$kodekost;
}

//---------------------- UPLOAD FOTO ----------------------\\
$isFoto = false;
if($foto == "0")
{
    $alamatfoto = $USER_IMAGE_FOLDER . 'noimage' . ".png";
    $isFoto = true;
}
else
{
    // if(move_uploaded_file($file_tmp, $target_file_to_move))
    if(file_put_contents("/home/pondokhu/public_html/Assets/images/user/" . $kode . ".jpg",base64_decode($foto)))
    {
        $alamatfoto = $USER_IMAGE_FOLDER . $kode . ".jpg";
        $isFoto = true;
    }
    else
    {
        echo json_encode(array('daftarpenyewa' => "upload gagal"));
    }
}

//---------------------- TAMBAH PENYEWA ----------------------\\
$isPenyewa = false;
if($isFoto)
{
    /*$query = "INSERT INTO tb_penyewa (kode, noktp, nama, tgllahir, jk, nohp, email, urlfoto, namaortu, nohportu, alamat, kelurahan, kecamatan, kotakab, provinsi, kodepos, tempatkuliahkerja, jurusankuliah, status, nomorpin, time_created) VALUES ('$kode', '$noktp', '$nama', '$tgllahir', '$jk', '$nohp', '$email', '$alamatfoto', '$namaortu', '$nohportu', '$alamat', '$kelurahan', '$kecamatan', '$kotakab', '$provinsi', '$kodepos', '$tempatkuliahkerja', '$jurkuliah', '$status', '$pinNum', '$inputDate')";*/
    
    $query = "INSERT INTO tb_penyewa (kode, noktp, nama, nohp, email, urlfoto, status, nomorpin, time_created, kode_kost) VALUES ('$kode', '$noktp', '$nama', '$nohp', '$email', '$alamatfoto', '$status', '$pinNum', '$inputDate', $kodekost)";
    
    $result = mysqli_query($koneksi, $query);
    
    if($result)
    {
        $isPenyewa = true;
    }
    else
    {
        echo json_encode(array("daftarpenyewa" => "error input penyewa baru ".mysqli_error($koneksi)));
    }
}

//---------------------- KIRIM EMAIL ----------------------\\
$isEmail = false;
if($isPenyewa)
{
    $querygetpenyewa = "SELECT kode, nama, email, nomorpin FROM tb_penyewa WHERE kode='$kode'";
        $resultpenyewa = mysqli_query($koneksi, $querygetpenyewa);
     
    $_kode;
    $_nama;
    $_email;
    $_nomorpin;
    
    if( mysqli_num_rows($resultpenyewa) > 0)
    {
        while($rows = mysqli_fetch_assoc($resultpenyewa))
        {
            $_kode = $rows['kode'];
            $_nama = $rows['nama'];
            $_email = $rows['email'];
            $_nomorpin = $rows['nomorpin'];
        }
        // echo "\nkode: " . $_kode;
        // echo "\nnama: " . $_nama;
        // echo "\nemail: " . $_email;
        // echo "\npin: " . $_nomorpin;
        
        $isEmail = ph_send_mail($_email, "Pendaftaran Penyewa Baru", "Selamat anda telah teregistrasi dalam sistem informasi Si Juragan Kost, atas nama " . $_nama . " :-) <br><br>Kode Akses: <b>" . $_kode . "</b><br>Nomor PIN: <b>" . $_nomorpin . "</b><br><br>Silahkan login dengan kode akses dan PIN tersebut pada <a href='https://pondok-huda.com/deploy/app/asharilabs/ysm0118123/ph2k18v0.apk'>Aplikasi Android Si Juragan Kost</a>, kemudian anda dapat mengubah nomor PIN tersebut (sesuaikan dengan keinginan anda) pada menu Biodata. TERIMA KASIH :) <br><<<NO-EMAIL-REPLY>>>", ph_html_mail_headers());
    }
}

// ------------------------END SEND EMAIL-----------------------------

// ------------------------PENYEWA KEDUA-----------------------------
if(isset($noktp2))
{
    // get kode generate for user -----------------------------------------
    $queryGetLastKodeInput = "SELECT kode FROM tb_penyewa
                              WHERE kode_kost='$kodekost'
                              ORDER BY time_created DESC LIMIT 0,1";
    $resultGetLastKodeInput = mysqli_query($koneksi, $queryGetLastKodeInput);
    
    if(mysqli_num_rows($resultGetLastKodeInput) > 0)
    {
        while($rows = mysqli_fetch_assoc($resultGetLastKodeInput))
        {
            $lastKode = $rows['kode'];
        }
        
        $kode2 = GenerateNextCode($lastKode, $kodekost);
        // echo "\nnext: " . $nextkode;
    }
    else
    {
        // random
        $rand1 = rand(0,70);
        $rand2 = rand(0,100);
        
        // get random 1, 2
        $kode2 = $rand1 < 45 ? "p" : "u";
        $kode2 = $rand2 < 60 ? $kode2 .= "a" : $kode2 .= "b";
        $kode2 .= '0001-'.$kodekost;
    }
    
    //---------------------- UPLOAD FOTO ----------------------\\
    $isFoto = false;
    if($foto2 == "0")
    {
        $alamatfoto = $USER_IMAGE_FOLDER . 'noimage' . ".png";
        $isFoto = true;
    }
    else
    {
        // if(move_uploaded_file($file_tmp, $target_file_to_move))
        if(file_put_contents("/home/pondokhu/public_html/Assets/images/user/" . $kode2 . ".jpg",base64_decode($foto2)))
        {
            $alamatfoto = $USER_IMAGE_FOLDER . $kode2 . ".jpg";
            $isFoto = true;
        }
        else
        {
            echo json_encode(array('daftarpenyewa' => "upload foto penyewa kedua gagal"));
        }
    }
    
    //---------------------- TAMBAH PENYEWA ----------------------\\
    $isPenyewa = false;
    if($isFoto)
    {
        /*$query = "INSERT INTO tb_penyewa (kode, noktp, nama, tgllahir, jk, nohp, email, urlfoto, namaortu, nohportu, alamat, kelurahan, kecamatan, kotakab, provinsi, kodepos, tempatkuliahkerja, jurusankuliah, status, nomorpin, time_created) VALUES ('$kode', '$noktp', '$nama', '$tgllahir', '$jk', '$nohp', '$email', '$alamatfoto', '$namaortu', '$nohportu', '$alamat', '$kelurahan', '$kecamatan', '$kotakab', '$provinsi', '$kodepos', '$tempatkuliahkerja', '$jurkuliah', '$status', '$pinNum', '$inputDate')";*/
        
        $query = "INSERT INTO tb_penyewa (kode, noktp, nama, nohp, email, urlfoto, status, nomorpin, time_created, kode_kost) VALUES ('$kode2', '$noktp2', '$nama2', '$nohp2', '$email2', '$alamatfoto', '$status2', '$pinNum2', '$inputDate', $kodekost)";
        
        $result = mysqli_query($koneksi, $query);
        
        if($result)
        {
            $isPenyewa = true;
        }
        else
        {
            echo json_encode(array("daftarpenyewa" => "error input penyewa kedua ".mysqli_error($koneksi)));
        }
    }
    
    //---------------------- KIRIM EMAIL ----------------------\\
    $isEmail = false;
    if($isPenyewa)
    {
        $querygetpenyewa = "SELECT kode, nama, email, nomorpin FROM tb_penyewa WHERE kode ='$kode2'";
            $resultpenyewa = mysqli_query($koneksi, $querygetpenyewa);
         
        $_kode;
        $_nama;
        $_email;
        $_nomorpin;
        
        if( mysqli_num_rows($resultpenyewa) > 0)
        {
            while($rows = mysqli_fetch_assoc($resultpenyewa))
            {
                $_kode = $rows['kode'];
                $_nama = $rows['nama'];
                $_email = $rows['email'];
                $_nomorpin = $rows['nomorpin'];
            }
            // echo "\nkode: " . $_kode;
            // echo "\nnama: " . $_nama;
            // echo "\nemail: " . $_email;
            // echo "\npin: " . $_nomorpin;
            
            $isEmail = ph_send_mail($_email, "Pendaftaran Penyewa Baru", "Selamat anda telah teregistrasi dalam sistem informasi PONDOK HUDA, atas nama " . $_nama . " :-) <br><br>Kode Akses: <b>" . $_kode . "</b><br>Nomor PIN: <b>" . $_nomorpin . "</b><br><br>Silahkan login dengan kode akses dan PIN tersebut pada <a href='https://pondok-huda.com/deploy/app/asharilabs/ysm0118123/ph2k18v0.apk'>Aplikasi Android Pondok Huda</a>, kemudian anda dapat mengubah nomor PIN tersebut (sesuaikan dengan keinginan anda) pada menu Biodata. TERIMA KASIH :) <br><<<NO-EMAIL-REPLY>>>", ph_html_mail_headers());
        }
    }
    
    // ------------------------END SEND EMAIL-----------------------------
}
// ------------------------END PENYEWA KEDUA-----------------------------

//---------------------- SEWA KAMAR ----------------------\\
$isSewa = false;
if($isEmail)
{
    $tanggalSelesai = date('Y-m-d');
    
    $tanggalmulai = date('Y-m-d');
    
    $day = date_parse_from_format("Y-m-d", $tanggalmulai);
    if ($day['day'] > 28)
    {
        $tanggalpembayaran = date('Y-m-01', strtotime('+1 month'));
    }
    else
    {
        $tanggalpembayaran = date('Y-m-d');
    }
    
    if(isset($noktp2))
    {
        $queryInsertSewaKamar = "INSERT INTO tb_sewa_kamar(kode_kamar, tanggal_mulai, harga_perbulan, tanggal_pembayaran, periode_bayar)
            VALUES ('$kodekamar', '$tanggalmulai', '$hargakamar', '$tanggalpembayaran', '$periodebayar')";
        
        $resultInsertSewaKamar = mysqli_query($koneksi, $queryInsertSewaKamar);
        
        if($resultInsertSewaKamar)
        {
            $kodesewa = mysqli_insert_id($koneksi);
    
            $queryPenyewaKamar = "INSERT INTO tb_penyewa_kamar(kode_sewa, kode_penyewa)
            VALUES ('$kodesewa', '$kode'), ('$kodesewa', '$kode2')";
    
            $resultPenyewaKamar = mysqli_query($koneksi, $queryPenyewaKamar);
    
            if($resultPenyewaKamar)
            {
                $isSewa = true;
            }
            else
            {
                echo json_encode(array("daftarpenyewa" => "error input penyewa kamar baru ".mysqli_error($koneksi)));
            }
        }
        else
        {
            echo json_encode(array("daftarpenyewa" => "error input data sewa  baru"));
        }
    }
    else
    {
        if($kamarterisi == "true")
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
                    $queryInsertSewaKamar = "INSERT INTO
                                            tb_sewa_kamar(kode_kamar, tanggal_mulai,
                                            harga_perbulan, tanggal_pembayaran, periode_bayar)
                                            VALUES ('$kodekamar', '$tanggalmulai',
                                            '$hargakamar', '$tanggalpembayaran', '$periodebayar')";
                
                    $resultInsertSewaKamar = mysqli_query($koneksi, $queryInsertSewaKamar);
                    
                    if($resultInsertSewaKamar)
                    {
                        $kodesewa = mysqli_insert_id($koneksi);
    
                        // ------- insert data penyewa tujuan -------\
                        $queryInsertPenyewaTujuan = "INSERT INTO
                        tb_penyewa_kamar(kode_sewa, kode_penyewa, kode_pindah)
                        SELECT '$kodesewa', kode_penyewa, '$kodePindahTujuan'
                        FROM tb_penyewa_kamar WHERE kode_sewa = (
                        SELECT kode_sewa FROM tb_pindah_kamar WHERE kode_pindah='$kodePindahTujuan')";
    
                        $resultInsertPenyewaTujuan = mysqli_query($koneksi, $queryInsertPenyewaTujuan);
    
                        if ($resultInsertPenyewaTujuan)
                        {
                            $queryPenyewaBaru = "INSERT INTO tb_penyewa_kamar(kode_sewa, kode_penyewa)
                            VALUES('$kodesewa', '$kode')";
                    
                            $resultPenyewaBaru = mysqli_query($koneksi, $queryPenyewaBaru);
                    
                            if($resultPenyewaBaru)
                            {
                                $isSewa = true;
                            }
                            else
                            {
                                echo json_encode(array("daftarpenyewa" => "error input penyewa baru kamar terisi"));
                            }
                        }
                        else
                        {
                            echo json_encode(array("daftarpenyewa" => "error input penyewa lama kamar terisi"));
                        }
                    }
                    else
                    {
                        echo json_encode(array("daftarpenyewa" => "error insert sewa kamar terisi"));
                    }
                }
                else
                {
                    echo json_encode(array("daftarpenyewa" => "error drop sewa tujuan"));
                }
            }
            else
            {
                echo json_encode(array("daftarpenyewa" => "error input pindah penyewa lama"));
            }
        }
        else
        {
            $queryInsertSewaKamar = "INSERT INTO tb_sewa_kamar(kode_kamar, tanggal_mulai, harga_perbulan, tanggal_pembayaran, periode_bayar)
            VALUES ('$kodekamar', '$tanggalmulai', '$hargakamar', '$tanggalpembayaran', '$periodebayar')";
        
            $resultInsertSewaKamar = mysqli_query($koneksi, $queryInsertSewaKamar);
            
            if($resultInsertSewaKamar)
            {
                $kodesewa = mysqli_insert_id($koneksi);
        
                $queryPenyewaKamar = "INSERT INTO tb_penyewa_kamar(kode_sewa, kode_penyewa)
                VALUES('$kodesewa', '$kode')";
        
                $resultPenyewaKamar = mysqli_query($koneksi, $queryPenyewaKamar);
        
                if($resultPenyewaKamar)
                {
                    $isSewa = true;
                }
                else
                {
                    echo json_encode(array("daftarpenyewa" => "error input penyewa kamar baru ".mysqli_error($koneksi)));
                }
            }
            else
            {
                echo json_encode(array("daftarpenyewa" => "error input data sewa  baru"));
            }
        }
    }
}

//---------------------- BAYAR SEWA KOST ----------------------\\
$isBayar = false;
if($isSewa)
{
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
    
    $queryBayarPertama = "INSERT INTO tb_bayar_kost
                         (kode_bayar, kode_sewa, tanggal_pembayaran,
                         tanggal_bayar, periode_bayar, harga_perbulan,
                         diskon, total_harga, total_bayar, metode)
                         VALUES('$kodebayar', '$kodesewa', '$tanggalpembayaran',
                         '$tanggalmulai', '$periodebayar', '$hargakamar',
                         '$diskon', '$totalharga', '$totalbayar', 'Tunai')";
                
    $resultBayarPertama = mysqli_query($koneksi, $queryBayarPertama);

    if($resultBayarPertama)
    {
        $isBayar = true;
    }
    else
    {
        echo json_encode(array("daftarpenyewa" => "error input bayar pertama"));
    }
}

if($isBayar)
{
    $queryinsertlaporankeu = "INSERT INTO tb_keu_jurnal_umum
                             (kode_transaksi, kode_akun, tanggal, keterangan,
                             posisi, jumlah, kode_kost)
                             
                             VALUES
                             
                             ('$kodebayar', '1-1100', '$tanggalmulai',
                             'Kas', 'Debit', $totalbayar, '$kodekost')";
    if($diskon != 0)
    {
        $queryinsertlaporankeu .= ",('$kodebayar', '1-0100', '$tanggalmulai',
                             'Diskon', 'Debit', $diskon, '$kodekost')";
    }
    
    $queryinsertlaporankeu .= ",('$kodebayar', '5-1100', '$tanggalmulai',
                             CONCAT('Pendapatan Sewa Kamar ', '$nokamar'),
                             'Kredit', $totalbayar + $diskon, '$kodekost')";

    $resultinsertlaporankeu = mysqli_query($koneksi, $queryinsertlaporankeu);
    
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
    if($resultinsertlaporankeu)
    {
        $cekemail = "";
        $namaEmail = "";
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
tb_bayar_kost.total_bayar,
tb_bayar_kost.total_bayar_sebelumnya,
tb_bayar_kost.sisa_bayar_sebelumnya,
tb_bayar_kost.total_harga,
tb_bayar_kost.metode,

IF(
(SELECT COUNT(*) FROM tb_bayar_kost WHERE tb_bayar_kost.kode_sewa = (SELECT tb_sewa_kamar.kode_sewa FROM tb_sewa_kamar WHERE tb_sewa_kamar.kode_kamar = '$kodekamar' AND tb_sewa_kamar.tanggal_selesai IS NULL LIMIT 0,1)) = 1,

CONCAT('Pembayaran pertama periode sewa ',
       
DATE_FORMAT((SELECT tb_bayar_kost.tanggal_pembayaran FROM tb_bayar_kost 
WHERE tb_bayar_kost.kode_sewa = (SELECT tb_sewa_kamar.kode_sewa FROM tb_sewa_kamar 
WHERE tb_sewa_kamar.kode_kamar = '$kodekamar' AND tb_sewa_kamar.tanggal_selesai IS NULL LIMIT 0,1)
ORDER BY tb_bayar_kost.kode_bayar DESC LIMIT 0,1),'%d/%m/%Y')
       
,' s.d. ',
DATE_FORMAT(DATE_ADD((SELECT tb_bayar_kost.tanggal_pembayaran FROM tb_bayar_kost 
WHERE tb_bayar_kost.kode_sewa = (SELECT tb_sewa_kamar.kode_sewa FROM tb_sewa_kamar 
WHERE tb_sewa_kamar.kode_kamar = '$kodekamar' AND tb_sewa_kamar.tanggal_selesai IS NULL LIMIT 0,1) 
ORDER BY tb_bayar_kost.kode_bayar DESC LIMIT 0,1), INTERVAL $periodebayar MONTH),'%d/%m/%Y')),

CONCAT(DATE_FORMAT((SELECT tb_bayar_kost.tanggal_pembayaran FROM tb_bayar_kost 
WHERE tb_bayar_kost.kode_sewa = (SELECT tb_sewa_kamar.kode_sewa FROM tb_sewa_kamar 
WHERE tb_sewa_kamar.kode_kamar = '$kodekamar' AND tb_sewa_kamar.tanggal_selesai IS NULL LIMIT 0,1)
ORDER BY tb_bayar_kost.kode_bayar DESC LIMIT 0,1),'%d/%m/%Y')
       
,' s.d. ',
DATE_FORMAT(DATE_ADD((SELECT tb_bayar_kost.tanggal_pembayaran FROM tb_bayar_kost 
WHERE tb_bayar_kost.kode_sewa = (SELECT tb_sewa_kamar.kode_sewa FROM tb_sewa_kamar 
WHERE tb_sewa_kamar.kode_kamar = '$kodekamar' AND tb_sewa_kamar.tanggal_selesai IS NULL LIMIT 0,1) 
ORDER BY tb_bayar_kost.kode_bayar DESC LIMIT 0,1), INTERVAL $periodebayar MONTH),'%d/%m/%Y'))    
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
            echo "ERROR QUERY " . mysqli_error($koneksi);
            exit;
        }
      
        for($i = 0; count($namaList) > $i; $i++)
        {
            if($namaEmail == "")
            {
                $namaEmail = $namaList[$i];
            }
            else
            {
                $namaEmail .= ", " . $namaList[$i];
                
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
            // remove default header/footer
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
                                                <td colspan="3" height="90px"></td>
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
        
        if(isset($noktp2))
        {
            if(isset($kodeadmin))
            {
                $log = CreateLogAdmin($koneksi, $namaadmin.' menambahkan penyewa di kamar '.$nokamar.' atas nama penyewa '.$nama.', '.$nama2, $kodeadmin, $kodekost);
            }
            
            if(isset($kodeowner))
            {
                $log = CreateLogOwner($koneksi, $namaowner.' menambahkan penyewa di kamar '.$nokamar.' atas nama penyewa '.$nama.', '.$nama2, $kodeowner, $kodekost);
            }
        }
        else
        {
            if(isset($kodeadmin))
            {
                $log = CreateLogAdmin($koneksi, $namaadmin.' menambahkan penyewa di kamar '.$nokamar.' atas nama penyewa '.$nama, $kodeadmin, $kodekost);
            }
            
            if(isset($kodeowner))
            {
                $log = CreateLogOwner($koneksi, $namaowner.' menambahkan penyewa di kamar '.$nokamar.' atas nama penyewa '.$nama, $kodeowner, $kodekost);
            }
        }
        
        echo json_encode(array("daftarpenyewa" => "penyewaterdaftar"));
    }
    else
    {
        echo json_encode(array("daftarpenyewa" => "error insert data laporan keuangan " . mysqli_error($koneksi)));
    }
}

/*if($isBayar)
{
$queryGetEmailPenyewa = "SELECT 
	tb_sewa_kamar.kode_sewa, 
   	tb_bayar_kost.kode_bayar, 
    tb_penyewa.nama, 
    tb_penyewa.email, 
    tb_bayar_kost.harga_perbulan,
    tb_bayar_kost.periode_bayar,
    tb_bayar_kost.denda,
    tb_bayar_kost.diskon,
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
    
    CONCAT(DATE_FORMAT((SELECT tb_bayar_kost.tanggal_pembayaran FROM tb_bayar_kost 
    WHERE tb_bayar_kost.kode_sewa = (SELECT tb_sewa_kamar.kode_sewa FROM tb_sewa_kamar 
    WHERE tb_sewa_kamar.kode_kamar = '$kodekamar' AND tb_sewa_kamar.tanggal_selesai IS NULL LIMIT 0,1)
    ORDER BY tb_bayar_kost.kode_bayar DESC LIMIT 0,1),'%d/%m/%Y')
           
    ,' s.d. ',
    DATE_FORMAT(DATE_ADD((SELECT tb_bayar_kost.tanggal_pembayaran FROM tb_bayar_kost 
    WHERE tb_bayar_kost.kode_sewa = (SELECT tb_sewa_kamar.kode_sewa FROM tb_sewa_kamar 
    WHERE tb_sewa_kamar.kode_kamar = '$kodekamar' AND tb_sewa_kamar.tanggal_selesai IS NULL LIMIT 0,1) 
    ORDER BY tb_bayar_kost.kode_bayar DESC LIMIT 0,1), INTERVAL $periodebayar MONTH),'%d/%m/%Y')
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
                                             
    // 2.1 bayar sebulan
    if ($periodebayar == '1')
    {
        $queryinsertlaporankeu = "INSERT INTO tb_keu_jurnal_umum
        (kode_transaksi, kode_akun, tanggal, keterangan,
        posisi, jumlah, kode_kost)
        VALUES(CONCAT('$kodebayar', '-1'),
        '1-1100', '$tanggalmulai', 'Kas',
        'Debit', $totalbayar, '$kodekost'),
        (CONCAT('$kodebayar', '-1'),
        '5-1100', '$tanggalmulai',
        CONCAT('Pendapatan Sewa Kamar ', $nokamar),
        'Kredit', '$totalbayar', '$kodekost')";

        $resultinsertlaporankeu = mysqli_query($koneksi, $queryinsertlaporankeu);
        
        if($resultinsertlaporankeu)
        {
          echo json_encode(array("daftarpenyewa" => "penyewaterdaftar"));
        }
        else
        {
            echo json_encode(array("daftarpenyewa" => "error insert data laporan keuangan (tunai, tepat waktu, 1 bulan)"));
        }
    }
    // 2.2 bayar lebih dari sebulan
    else
    {
        $queryinsertlaporankeu = "INSERT INTO tb_keu_jurnal_umum
                            (kode_transaksi,
                            kode_akun, tanggal, keterangan,
                            posisi, jumlah, kode_kost)
                            VALUES(CONCAT('$kodebayar', '-3'),
                            '1-1100', '$tanggalmulai', 'Kas',
                            'Debit', $totalbayar, '$kodekost'),
                            (CONCAT('$kodebayar', '-3'),
                            '1-1400', '$tanggalmulai',
                            CONCAT('Sewa Diterima Di Muka Kamar ', $nokamar),
                            'Kredit', $totalbayar + $diskon, '$kodekost')";
  
        $resultinsertlaporankeu = mysqli_query($koneksi, $queryinsertlaporankeu);
        
        if($resultinsertlaporankeu)
        {
          echo json_encode(array("daftarpenyewa" => "penyewaterdaftar"));
        }
        else
        {
            echo json_encode(array("daftarpenyewa" => "error insert data laporan keuangan (tunai, tepat waktu, sddm)"));
        }
    }
}*/
// }


// -------------------------------------------------


function GenerateNextCode($str, $kodekost)
{
    // kode = KW-thn/bln/nokamar/nourut (no urut = 6 digit)
    $noUrutTerakhir = substr($str, 2, 4);
    
    $noUrutBaru =  $noUrutTerakhir + 1;
    
    // random
    $rand1 = rand(0,70);
    $rand2 = rand(0,100);
    
    // get random 1, 2
    $hasil = $rand1 < 45 ? "p" : "u";
    $hasil = $rand2 < 60 ? $hasil .= "a" : $hasil .= "b";
    $hasil .= substr($noUrutTerakhir, 0, -1 * abs(strlen($noUrutBaru))).$noUrutBaru.'-'.$kodekost;
    
    return $hasil;
}

function GetRandomPIN()
{
    $pin = rand(0,9);
    
    for($i = 0; $i < 5; $i++)
    {
        $pin .= rand(0,9);
    }
    return $pin;
}

function UploadImage()
{
    
}

function GenerateKodePembayaran($kode, $nokamar, $kodekost)
{
    // kode = KW-thn/bln/nokamar/nourut (no urut = 6 digit)
    $noUrutTerakhir = substr($kode, -6);
    
    $noUrutBaru =  $noUrutTerakhir + 1;
    
    $hasil = 'KW-'.date('Y/m/').$nokamar.'/'.$kodekost.'/'.substr($noUrutTerakhir, 0, -1 * abs(strlen($noUrutBaru))).$noUrutBaru;
    
    return $hasil;
}
?>
