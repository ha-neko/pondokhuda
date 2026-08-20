<?php
include('kon.php');
include ("admin_log.php");
include ("owner_log.php");

$kodekel = $_POST['_kodekeluhan'];
$komen = $_POST['_komen'];

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
$kodekost = $_POST['_kodekost'];

$koneksi = mysqli_connect($host, $user, $pass, $daba);
    
if( !$koneksi)
{
	return;
}

$querygetkeluhan = "SELECT judul, tb_penyewa.nama
                    
                    FROM tb_keluhan_list
                    
                    INNER JOIN tb_penyewa
                    ON tb_keluhan_list.user =  tb_penyewa.kode
                    
                    WHERE tb_keluhan_list.id='$kodekel'";

$resultgetkeluhan = mysqli_query($koneksi, $querygetkeluhan);

if( mysqli_num_rows($resultgetkeluhan) == 1)
{
    while($rows = mysqli_fetch_assoc($resultgetkeluhan))
    {
        $judul = $rows['judul'];
        $nama = $rows['nama'];
    }
}

$tglkomen = date('Y-m-d H:i:s');

$query = "INSERT INTO tb_keluhan_komenadmin(kode_kel, komen, created) VALUES ('$kodekel', '$komen', '$tglkomen')";

$result = mysqli_query($koneksi, $query);

if($result)
{
    if(isset($kodeadmin))
    {
        $log = CreateLogAdmin($koneksi, $namaadmin.' menambahkan komen di keluhan '.$judul.', atas nama penyewa '.$nama, $kodeadmin, $kodekost);
    }
    if(isset($kodeowner))
    {
        $log = CreateLogOwner($koneksi, $namaowner.' menambahkan komen di keluhan '.$judul.', atas nama penyewa '.$nama, $kodeowner, $kodekost);
    }
    
    echo json_encode(array('keluhan' => "komen berhasil ditambah"));
}
else
{
    $log = CreateLogAdmin($koneksi, $namaadmin.' menambahkan komen di keluhan '.$judul.', atas nama penyewa '.$nama.'. Error: '.mysqli_error($koneksi), $kodeadmin, $kodekost);
    echo json_encode(array('keluhan' => "error: ".mysqli_error($koneksi)));
}

?>