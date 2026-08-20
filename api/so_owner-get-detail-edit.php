<?php

include ("kon.php");

$kodeowner = $_POST['_kodeowner'];

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if( !$koneksi)
{
    return;
}

$admin = array();

// get data semua kamar -----------------------------------------
$querygetowner = "SELECT kode, nama, nomor_telepon, email

                  FROM tb_owner
                  
                  WHERE tb_owner.kode = '$kodeowner'";
                  
$resultgetowner = mysqli_query($koneksi, $querygetowner);
if( mysqli_num_rows($resultgetowner) > 0)
{
    while($rows = mysqli_fetch_assoc($resultgetowner))
    {
        $owner = array(
            "kode"          => $rows['kode'],
            "nama"          => $rows['nama'],
            "nomortelepon"  => $rows['nomor_telepon'],
            "email"         => $rows['email'],
        );
    }
    
}
else
{
    $owner = null;
}

echo json_encode(array('owner' => $owner));

?>