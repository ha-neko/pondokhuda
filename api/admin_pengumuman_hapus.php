<?php
include('kon.php');

$kode = $_POST['_kode'];

if( $kode != "")
{
    $koneksi = mysqli_connect($host, $user, $pass, $daba);
    
    if (!$koneksi)
{
    header('Content-Type: application/json');
    echo json_encode(array('pengumuman' => 'koneksi database gagal'));
    return;
}
    
    $query = "DELETE FROM tb_beritakost WHERE kode = '$kode'";
    
    $result = mysqli_query($koneksi, $query);
    
    if( $result)
    {
        echo json_encode(array('pengumuman' => "hapusberhasil"));
    }
    else
    {
        echo json_encode(array('pengumuman' => "hapusgagal"));
    }
}
else
{
    echo json_encode(array('pengumuman' => "tidak ada data"));
}
?>