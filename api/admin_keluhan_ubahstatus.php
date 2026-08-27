<?php

include ("kon.php");
include ("admin_log.php");
include ("owner_log.php");

$kode = $_POST['_kode'];
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

date_default_timezone_set("Asia/Bangkok");
$tgl = date('Y-m-d H:i:s');

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if (!$koneksi)
{
    header('Content-Type: application/json');
    echo json_encode(array('ubahstatuskeluhan' => 'koneksi database gagal'));
    return;
}

$q1 = "SELECT * FROM tb_keluhan_list

       INNER JOIN tb_penyewa
       ON tb_keluhan_list.user=tb_penyewa.kode
       
       WHERE tb_keluhan_list.id = '$kode'";
       
$r1 = mysqli_query($koneksi, $q1);

if( mysqli_num_rows($r1) == 1)
{
    while($rows = mysqli_fetch_assoc($r1))
    {
        $judul = $rows['judul'];
        $namapenyewa = $rows['nama'];
    }
    
    $q = "SELECT * FROM tb_keluhan_updatetgl WHERE kode = '$kode' AND status = '$status'";
    $res = mysqli_query($koneksi, $q);   
    
    if( mysqli_num_rows($res) == 0)
    {
        $query = "INSERT INTO tb_keluhan_updatetgl(kode, status, tglupdate) VALUES('$kode','$status','$tgl')";
        $result = mysqli_query($koneksi, $query);
        
        if( $result)
        {
            if(isset($kodeadmin))
            {
                $log = CreateLogAdmin($koneksi, $namaadmin.' mengubah status keluhan '.$judul.', atas nama penyewa '.$namapenyewa.' menjadi '.$status, $kodeadmin, $kodekost);
            }
            
            if(isset($kodeowner))
            {
                $log = CreateLogOwner($koneksi, $namaowner.' mengubah status keluhan '.$judul.', atas nama penyewa '.$namapenyewa.' menjadi '.$status, $kodeowner, $kodekost);
            }
            
            echo json_encode(array('ubahstatuskeluhan' => "status keluhan sukses diperbarui"));
        }
        else
        {
            if(isset($kodeadmin))
            {
                $log = CreateLogAdmin($koneksi, $namaadmin.' mengubah status keluhan '.$judul.', atas nama penyewa '.$namapenyewa.' menjadi '.$status.'. Error: '.mysqli_error($koneksi), $kodeadmin, $kodekost);
            }
            
            if(isset($kodeowner))
            {
                $log = CreateLogAdmin($koneksi, $namaowner.' mengubah status keluhan '.$judul.', atas nama penyewa '.$namapenyewa.' menjadi '.$status.'. Error: '.mysqli_error($koneksi), $kodeowner, $kodekost);
            }
            
            echo json_encode(array('ubahstatuskeluhan' => "gagal diperbarui"));
        }
    }
    else
    {
        if(isset($kodeadmin))
        {
            $log = CreateLogAdmin($koneksi, $namaadmin.' mengubah status keluhan '.$judul.', atas nama penyewa '.$namapenyewa.' menjadi '.$status, $kodeadmin, $kodekost);
        }
        
        if(isset($kodeowner))
        {
            $log = CreateLogOwner($koneksi, $namaowner.' mengubah status keluhan '.$judul.', atas nama penyewa '.$namapenyewa.' menjadi '.$status, $kodeowner, $kodekost);
        }
        
        echo json_encode(array('ubahstatuskeluhan' => "data ganda"));
    }
}
else
{
    echo json_encode(array('ubahstatuskeluhan' => "data keluhan tidak ditemukan"));
}
?>