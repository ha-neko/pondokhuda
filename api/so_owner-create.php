<?php

include ("kon.php");

//$kode = $_POST['_kode'];
$nama = $_POST['_nama'];
$pin = GetRandomPIN();
$notelp = $_POST['_notelp'];
$foto = $_POST['_foto'];
$email = $_POST['_email'];

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if( !$koneksi)
{
    return;
}

// get data semua kamar -----------------------------------------
$querygetkode = "SELECT kode FROM tb_owner ORDER BY kode DESC LIMIT 0,1";
                    
$resultgetkode = mysqli_query($koneksi, $querygetkode);
if( mysqli_num_rows($resultgetkode) > 0)
{
    while($rows = mysqli_fetch_assoc($resultgetkode))
    {
        $kode = GetNextKode($rows['kode']);
    }
}
else
{
    $kode = "o000w";
}

$IMAGE_FOLDER = "/Assets/images/owner/";

$isUpload = false;

if($foto != "0")
{
    if(file_put_contents("/home/pondokhu/public_html/Assets/images/owner/" . $kode . ".jpg",base64_decode($foto)))
    {
        $isUpload = true;
        $urlfoto = $IMAGE_FOLDER . $kode . ".jpg";
    }
    else
    {
        $owner = array('owner' => "upload gagal");
    }
}
else
{
    $isUpload = true;
    $urlfoto = $IMAGE_FOLDER . 'noimage' . ".png";
}
$kodeOwner = "";
$tgldaftar = date('Y-m-d');
$isOwner = false;
if($isUpload)
{
    $queryinsertowner = "INSERT INTO tb_owner
                        (kode, nama, pin, nomor_telepon, email, tanggal_terdaftar, urlfoto)
                        VALUES ('$kode', '$nama', '$pin', '$notelp', '$email',  '$tgldaftar', '$urlfoto')";
                
    $resultinsertowner = mysqli_query($koneksi, $queryinsertowner);
    
    if($resultinsertowner)
    {
        $isOwner = true;
        $kodeOwner = $kode;                
    }
    else
    {
        $owner = array('owner' => 'gagal tambah owner, Error: '. mysqli_error($koneksi));
    }
}

//---------------------- KIRIM EMAIL ----------------------\\
if($isOwner)
{
    $querygetowner = "SELECT kode, nama, email, pin FROM tb_owner WHERE kode='$kodeOwner'";
        $resultowner = mysqli_query($koneksi, $querygetowner);
     
    $_kode;
    $_nama;
    $_email;
    $_nomorpin;
    
    if( mysqli_num_rows($resultowner) > 0)
    {
        while($rows = mysqli_fetch_assoc($resultowner))
        {
            $_kode = $rows['kode'];
            $_nama = $rows['nama'];
            $_email = $rows['email'];
            $_nomorpin = $rows['pin'];
        }
        // echo "\nkode: " . $_kode;
        // echo "\nnama: " . $_nama;
        // echo "\nemail: " . $_email;
        // echo "\npin: " . $_nomorpin;
        
        try
        {
            $header = "From: noreply @pondok-huda.com" . "\r\n";
            $header .= 'Content-type: text/html; charset=utf-8' . "\r\n";
            
            mail($_email, "Pendaftaran Owner Baru", "Selamat anda telah teregistrasi dalam sistem informasi PONDOK HUDA, atas nama " . $_nama . " :-) <br><br>Kode Akses: <b>" . $_kode . "</b><br>Nomor PIN: <b>" . $_nomorpin . "</b><br><br>Silahkan login dengan kode akses dan PIN tersebut pada <a href='https://pondok-huda.com/'>Aplikasi Si Jurangan Kost</a>. TERIMA KASIH :) <br><<<NO-EMAIL-REPLY>>>", $header);
            
            $owner = array('owner' => 'owner berhasil ditambah');
        }
        catch(Exception $ex)
        {
            echo json_encode(array("error_send_email" => "errorsendemail: " . $ex->getMessage()));
        }
    }
}

// ------------------------END SEND EMAIL-----------------------------

echo json_encode(array('owner' => $owner));

function GetNextKode($kode)
{
    $noUrutTerakhir = substr($kode, 1, 3);
    
    $noUrutBaru =  $noUrutTerakhir + 1;
    
    $hasil = 'o'.substr($noUrutTerakhir, 0, -1 * abs(strlen($noUrutBaru))).$noUrutBaru.'w';
    
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

?>