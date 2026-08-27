<?php

include ("kon.php");
include("owner_log.php");

$kode = $_POST['_kode'];
$nama = $_POST['_nama'];
$pin = $_POST['_pin'];
$notelp = $_POST['_notelp'];
$foto = $_POST['_foto'];
$kodekost = $_POST['_kodekost'];
$listkost = $_POST['_listkost'];
$kodeowner = $_POST['_kodeowner'];
$namaowner = $_POST['_namaowner'];

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if (!$koneksi)
{
    header('Content-Type: application/json');
    echo json_encode(array('error' => 'koneksi database gagal'));
    return;
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
    $querygetfoto = "SELECT * FROM tb_admin
                    WHERE kode='$kode'";
                        
    $resultgetfoto = mysqli_query($koneksi, $querygetfoto);
    if( mysqli_num_rows($resultgetfoto) > 0)
    {
        while($rows = mysqli_fetch_assoc($resultgetfoto))
        {
            $urlfoto   = $rows['urlfoto'];
        }
        $isUpload = true;
    }
    else
    {
        $admin = array('admin' => "gagal get foto admin, Error". mysqli_error($koneksi));
    }
}

$isUpdate = false;
if($isUpload)
{
    $queryupdateadmin = "UPDATE tb_admin SET kode='$kode', nama='$nama',
                        pin='$pin', nomor_telepon='$notelp', urlfoto='$urlfoto'
                        WHERE kode='$kode'";
                
    $resultupdateadmin = mysqli_query($koneksi, $queryupdateadmin);
    
    if($resultupdateadmin)
    {
        $isUpdate = true;
        $admin = array('admin' => "admin berhasil diubah");
    }
    else
    {
        $admin = array('admin' => 'gagal ubah data admin, Error: '. mysqli_error($koneksi));
    }
}

if($isUpdate)
{
    if($listkost != 0)
    {
        if($kodekost != 0)
        {
            foreach($listkost as $kost)
            {
                if(!in_array($kost, $kodekost))
                {
                    $queryadminkost = "DELETE FROM tb_admin_kost
                    WHERE kode_admin='$kode' AND kode_kost='$kost'";
                    
                    $resultadminkost = mysqli_query($koneksi, $queryadminkost);
                    
                    if($resultadminkost)
                    {
                        $admin = array('admin' => "admin berhasil diubah");
                    }
                    else
                    {
                        $admin = array('admin' => 'gagal menempatkan admin, Error: '. mysqli_error($koneksi));
                    }
                }
            }
            
            foreach($kodekost as $kost)
            {
                if(!in_array($kost, $listkost))
                {
                    $queryadminkost = "INSERT INTO tb_admin_kost
                           (kode_kost, kode_admin)
                           VALUES ('$kost', '$kode')";
                
                    $resultadminkost = mysqli_query($koneksi, $queryadminkost);
                    
                    if($resultadminkost)
                    {
                        $admin = array('admin' => "admin berhasil diubah");
                    }
                    else
                    {
                        $admin = array('admin' => 'gagal menempatkan admin, Error: '. mysqli_error($koneksi));
                    }
                }
            }
        }
        else
        {
            foreach($listkost as $kost)
            {
                $queryadminkost = "DELETE FROM tb_admin_kost
                WHERE kode_admin='$kode' AND kode_kost='$kost'";
                
                $resultadminkost = mysqli_query($koneksi, $queryadminkost);
                
                if($resultadminkost)
                {
                    $admin = array('admin' => "admin berhasil diubah");
                }
                else
                {
                    $admin = array('admin' => 'gagal menempatkan admin, Error: '. mysqli_error($koneksi));
                }
            }
        }
    }
    else
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
                    $admin = array('admin' => "admin berhasil diubah");
                }
                else
                {
                    $admin = array('admin' => 'gagal menempatkan admin, Error: '. mysqli_error($koneksi));
                }
            }
        }
        else
        {
            $admin = array('admin' => "admin berhasil diubah");
        }
    }
}

$log = CreateLogOwner($koneksi, $namaowner.' mengubah data admin atas nama '.$nama, $kodeowner, NULL);

echo json_encode($admin);
/*function GetRandomPIN()
{
    $pin = rand(0,9);
    
    for($i = 0; $i < 5; $i++)
    {
        $pin .= rand(0,9);
    }
    return $pin;
}*/

?>