<?php

include ("kon.php");
include("owner_log.php");

$kodekost = $_POST['_kodekost'];
$namakost = $_POST['_namakost'];
$alamatkost = $_POST['_alamatkost'];
$emailkost = $_POST['_emailkost'];
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

$ceklogo = false;
$USER_IMAGE_FOLDER = "/Assets/images/logo/";
if($logo != "noimage")
{
    unlink("../Assets/images/logo/" . $kodekost . ".png");
    
    if(file_put_contents("/home/pondokhu/public_html/Assets/images/logo/" . $kodekost . ".png",base64_decode($logo)))
    {
        $ceklogo = true;
        $alamatlogo = $USER_IMAGE_FOLDER . $kodekost . ".png";
    }
    else
    {
        $kost = array('kost' => "upload gagal");
    }
}
else
{
    $querygetfoto = "SELECT * FROM tb_kost
                     WHERE kode_kost='$kodekost'";
                        
    $resultgetfoto = mysqli_query($koneksi, $querygetfoto);
    if( mysqli_num_rows($resultgetfoto) > 0)
    {
        while($rows = mysqli_fetch_assoc($resultgetfoto))
        {
            $alamatlogo   = $rows['logo'];
        }
        $ceklogo = true;
    }
    else
    {
        $kost = array('kost' => "gagal get foto kost, Error". mysqli_error($koneksi));
    }
}

if($ceklogo) 
{
    $queryupdatekost = "UPDATE tb_kost SET nama_kost='$namakost', alamat='$alamatkost', emailkost='$emailkost', primarycolor='$primarycolor', name_color='$namecolor', logo='$alamatlogo' WHERE kode_kost='$kodekost'";
    
    $resultupdatekost = mysqli_query($koneksi, $queryupdatekost);

    if($resultupdatekost)
    {
        $log = CreateLogOwner($koneksi, $namaowner.' mengubah data kost atas nama kost '.$namakost, $kodeowner, $kodekost);
        $kost = array('kost' => 'kost berhasil diupdate');   
    }
    else
    {
        $log = CreateLogOwner($koneksi, $namaowner.' mengubah data kost atas nama kost '.$namakost.'. Error: '.mysqli_error($koneksi), $kodeowner, $kodekost);
        $kost = array('kost' => 'gagal update kost, Error: '. mysqli_error($koneksi));
    }   
}
else
{
    $log = CreateLogOwner($koneksi, $namaowner.' mengubah data kost atas nama kost '.$namakost.'. Error: gagal update logo', $kodeowner, $kodekost);
    $kost = array('kost' => 'gagal update logo kost');
}

echo json_encode($kost);

?>