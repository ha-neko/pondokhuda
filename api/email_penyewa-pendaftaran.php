<?php

include ("kon.php");

$koneksi = mysqli_connect($host, $user, $pass, $daba);

$kode = $_POST['_kode'];

if( !$koneksi)
{
    return;
}

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
    
    try
    {
        $header = "From: noreply @pondok-huda.com" . "\r\n";
        $header .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        
        mail($_email, "Pendaftaran Penyewa Baru", "Selamat anda telah teregistrasi dalam sistem informasi Si Juragan Kost, atas nama " . $_nama . " :-) <br><br>Kode Akses: <b>" . $_kode . "</b><br>Nomor PIN: <b>" . $_nomorpin . "</b><br><br>Silahkan login dengan kode akses dan PIN tersebut pada <a href='https://pondok-huda.com/deploy/app/asharilabs/ysm0118123/ph2k18v0.apk'>Aplikasi Android Si Juragan Kost</a>, kemudian anda dapat mengubah nomor PIN tersebut (sesuaikan dengan keinginan anda) pada menu Biodata. TERIMA KASIH :) <br><<<NO-EMAIL-REPLY>>>", $header);
        
        echo json_encode(array('email' => 'email berhasil dikirim'));
    }
    catch(Exception $ex)
    {
        echo json_encode(array('email' => "error kirim email: ".$ex->getMessage()));
    }
}

?>