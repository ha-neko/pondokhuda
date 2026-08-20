<?php

include ("kon.php");
include("owner_log.php");

$namakost = $_POST['_namakost'];
$alamatkost = $_POST['_alamatkost'];
$emailkost = $_POST['_emailkost'];
$kodeowner = $_POST['_kodeowner'];
$primarycolor = $_POST['_primarycolor'];
$namecolor = $_POST['_namecolor'];
$logo = $_POST['_logo'];
$kodeowner = $_POST['_kodeowner'];
$namaowner = $_POST['_namaowner'];

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if( !$koneksi)
{
    return;
}

$USER_IMAGE_FOLDER = "/Assets/images/logo/";
$queryinsertkost = "INSERT INTO tb_kost
                    (nama_kost, alamat, emailkost, primarycolor, name_color)
                    VALUES
                    ('$namakost', '$alamatkost', '$emailkost', '$primarycolor', '$namecolor')";
$resultinsertkost = mysqli_query($koneksi, $queryinsertkost);

if($resultinsertkost)
{
    $kodekost = mysqli_insert_id($koneksi);
    
    $queryownerkost = "INSERT INTO tb_owner_kost
                       (kode_kost, kode_owner)
                       VALUES ('$kodekost', '$kodeowner')";
            
    $resultownerkost = mysqli_query($koneksi, $queryownerkost);
    
    if($resultownerkost)
    {
        if($logo != "noimage")
        {
            $alamatlogo = $USER_IMAGE_FOLDER . $kodekost . ".png";
            $queryupdatelogo = "UPDATE tb_kost SET logo = '$alamatlogo' WHERE kode_kost='$kodekost'";
            
            file_put_contents("/home/pondokhu/public_html/Assets/images/logo/" . $kodekost . ".png",base64_decode($logo));
            $resultupdatelogo = mysqli_query($koneksi, $queryupdatelogo);
            if($resultupdatelogo)
            {
                $log = CreateLogOwner($koneksi, $namaowner.' menambahkan kost dengan nama '.$namakost, $kodeowner, $kodekost);
                $kost = array('kost' => 'kost berhasil ditambah');
            }
        }
        else
        {
            $log = CreateLogOwner($koneksi, $namaowner.' menambahkan kost dengan nama '.$namakost, $kodeowner, $kodekost);
            $kost = array('kost' => 'kost berhasil ditambah');
        }
    }
    else
    {
        $log = CreateLogOwner($koneksi, $namaowner.' menambahkan kost dengan nama '.$namakost.'. Error: '.mysqli_error($koneksi), $kodeowner, $kodekost);
        $kost = array('kost' => 'gagal menambahkan owner kost, Error: '. mysqli_error($koneksi));
    }
}
else
{
    $log = CreateLogOwner($koneksi, $namaowner.' menambahkan kost dengan nama '.$namakost.'. Error: '.mysqli_error($koneksi), $kodeowner, $kodekost);
    $kost = array('kost' => 'gagal menambahkan kost, Error: '. mysqli_error($koneksi));
}

echo json_encode($kost);

?>