<?php

include ("kon.php");
include("owner_log.php");

//$kode = $_POST['_kode'];
$nama = $_POST['_nama'];
$pin = $_POST['_pin'];
$notelp = $_POST['_notelp'];
$foto = $_POST['_foto'];
$kodeowner = $_POST['_kodeowner'];
$namaowner = $_POST['_namaowner'];
$kodekost = $_POST['_kodekost'];

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if (!$koneksi)
{
    header('Content-Type: application/json');
    echo json_encode(array('admin' => 'koneksi database gagal'));
    return;
}

// get kode terakhir -----------------------------------------
$querygetkode = "SELECT kode FROM tb_admin ORDER BY kode DESC LIMIT 0,1";
                    
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
    $kode = "a000d";
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

$IMAGE_FOLDER = "/Assets/images/admin/";

$isUpload = false;

if($foto != "0")
{
    if(file_put_contents("/home/pondokhu/public_html/Assets/images/admin/" . $kode . ".jpg",base64_decode($foto)))
    {
        $isUpload = true;
        $urlfoto = $IMAGE_FOLDER . $kode . ".jpg";
    }
    else
    {
        $admin = array('admin' => "upload gagal");
    }
}
else
{
    $isUpload = true;
    $urlfoto = $IMAGE_FOLDER . 'noimage' . ".png";
}

if($isUpload)
{
    $queryinsertadmin = "INSERT INTO tb_admin
                        (kode, nama, pin, nomor_telepon, urlfoto, kode_owner)
                        VALUES ('$kode', '$nama', '$pin', '$notelp', '$urlfoto', '$kodeowner')";
                
    $resultinsertadmin = mysqli_query($koneksi, $queryinsertadmin);
    
    if($resultinsertadmin)
    {
        if($kodekost != 0)
        {
            foreach($kodekost as $kost)
            {
                $queryadminkost = "INSERT INTO tb_admin_kost
                           (kode_kost, kode_admin)
                           VALUES ('$kost', '$kode')";
                
                $resultadminkost = mysqli_query($koneksi, $queryadminkost);
                
                if($resultadminkost)
                {
                    $admin[] = array('admin'  => 'admin berhasil ditambah',
                                     'kode'   => $kode,
                                     'pin'    => $pin);
                }
                else
                {
                    $admin = array('admin' => 'gagal menempatkan admin, Error: '. mysqli_error($koneksi));
                }
            }
        }
        else
        {
            $admin[] = array('admin'  => 'admin berhasil ditambah',
                             'kode'   => $kode,
                             'pin'    => $pin);
        }
    }
    else
    {
        $admin = array('admin' => 'gagal tambah admin, Error: '. mysqli_error($koneksi));
    }
}

$log = CreateLogOwner($koneksi, $namaowner.' menambahkan admin dengan nama '.$nama, $kodeowner, NULL);

echo json_encode(array('admin' => $admin));

function GetNextKode($kode)
{
    $noUrutTerakhir = substr($kode, 1, 3);
    
    $noUrutBaru =  $noUrutTerakhir + 1;
    
    $hasil = 'a'.substr($noUrutTerakhir, 0, -1 * abs(strlen($noUrutBaru))).$noUrutBaru.'d';
    
    return $hasil;
}

?>