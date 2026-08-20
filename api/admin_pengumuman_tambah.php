<?php
include('kon.php');
include ("admin_log.php");
include ("owner_log.php");

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

// $judul = "Rekreasi";
// $berita = "Untuk seluruh penghuni kostan pondok huda, pada hari minggu besok akan dilaksanakan rekreasi ke pantai pangandaran dalam rangka ulang tahun pondok huda yang ke-10";

// $status = "tampil";

if( $judul != "" && $berita != "" && $status != "")
{
    $koneksi = mysqli_connect($host, $user, $pass, $daba);
    
    if( !$koneksi)
    {
    	return;
    }
    
    $tglpublish = date('Y-m-d');
    
    $query = "INSERT INTO tb_beritakost(judul, berita, tglpublish, lastupdate, status, kode_kost) VALUES ('$judul', '$berita', '$tglpublish', '$tglpublish', '$status', '$kodekost')";
    $result = mysqli_query($koneksi, $query);
    
    if( $result)
    {
        echo json_encode(array('pengumuman' => "databerhasilditambah"));
        
        if(isset($kodeadmin))
        {
            $log = CreateLogAdmin($koneksi, $namaadmin.' menginput pengumuman dengan judul: '.$judul.' dan status'.$status, $kodeadmin, $kodekost);
        }
        
        if(isset($kodeowner))
        {
            $log = CreateLogOwner($koneksi, $namaowner.' menginput pengumuman dengan judul: '.$judul.' dan status'.$status, $kodeowner, $kodekost);
        }
    }
    else
    {
        if(isset($kodeadmin))
        {
            $log = CreateLogAdmin($koneksi, $namaadmin.' menginput pengumuman dengan judul: '.$judul.' dan status'.$status.' Error: '.mysqli_error($koneksi), $kodeadmin, $kodekost);
        }
        
        if(isset($kodeowner))
        {
            $log = CreateLogOwner($koneksi, $namaowner.' menginput pengumuman dengan judul: '.$judul.' dan status'.$status.' Error: '.mysqli_error($koneksi), $kodeowner, $kodekost);
        }
        
        echo json_encode(array('pengumuman' => "error: ".mysqli_error($koneksi)));
    }
}
else
{
    echo json_encode(array('pengumuman' => "tidak ada data"));
}

?>