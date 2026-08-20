<?php

include ("kon.php");

$koneksi = mysqli_connect($host, $user, $pass, $daba);
$berita = $_POST['_berita'];

if( !$koneksi)
{
    return;
}

if( $berita != "" )
{
    $koneksi = mysqli_connect($host, $user, $pass, $daba);
    
    if( !$koneksi)
    {
    	return;
    }

	$hasil = array();
    
    $query = "SELECT * FROM tb_beritakost WHERE berita LIKE '%$berita%'";
    
    $result = mysqli_query($koneksi, $query);
    
    if( mysqli_num_rows($result) > 0)
	{
	    while($rows = mysqli_fetch_assoc($result))
	    {
	        $hasil[] = $rows;
	    }
	    
	    $hasil = json_encode(array('pengumuman' => $hasil));
	    echo $hasil;
	}
    else
    {
        echo json_encode(array('pengumuman' => "tidak ada data"));
    }
}
else
{
    echo json_encode(array('pengumuman' => "data belum lengkap"));
}

?>