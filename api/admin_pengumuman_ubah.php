<?php
include('kon.php');
include ("admin_log.php");
include ("owner_log.php");

$kode = $_POST['_kode'];
$judul = $_POST['_judul'];
$berita = $_POST['_berita'];
$status = $_POST['_status'];

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

// $kode = '1';
// $berita = "Perubahan skema pembayaran kostan untuk Bulan April 2018 dengan menggunakan transfer bank serta aplikasi pondok huda";

// $status = "tampil";

if( $kode != "" && $berita != "" && $status != "")
{
    $koneksi = mysqli_connect($host, $user, $pass, $daba);
    
    if (!$koneksi)
{
    header('Content-Type: application/json');
    echo json_encode(array('pengumuman' => 'koneksi database gagal'));
    return;
}
    
    $tglupdate = date('Y-m-d');
    
    $query = "UPDATE tb_beritakost SET berita = '$berita', lastupdate = '$tglupdate', status = '$status' WHERE kode = '$kode'";
    
    $result = mysqli_query($koneksi, $query);
    
    if( $result)
    {
        if(isset($kodeadmin))
        {
            $log = CreateLogAdmin($koneksi, $namaadmin.' mengubah pengumuman '.$judul, $kodeadmin, $kodekost);
        }
        
        if(isset($kodeowner))
        {
            $log = CreateLogOwner($koneksi, $namaowner.' mengubah pengumuman '.$judul, $kodeowner, $kodekost);
        }
        
        echo json_encode(array('pengumuman' => "updateberhasil"));
    }
    else
    {
        if(isset($kodeadmin))
        {
            $log = CreateLogAdmin($koneksi, $namaadmin.' mengubah pengumuman '.$judul.' Error: '.mysqli_error($koneksi), $kodeadmin, $kodekost);
        }
        
        if(isset($kodeowner))
        {
            $log = CreateLogOwner($koneksi, $namaowner.' mengubah pengumuman '.$judul.' Error: '.mysqli_error($koneksi), $kodeowner, $kodekost);
        }
        
        echo json_encode(array('pengumuman' => "updategagal"));
    }
}
else
{
    echo json_encode(array('pengumuman' => "tidak ada data"));
}
?>