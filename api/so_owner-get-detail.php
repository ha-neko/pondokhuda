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
$querygetowner = "SELECT nama, nomor_telepon, email,
                  DATE_FORMAT(tanggal_terdaftar,'%d/%m/%Y')
                  AS tanggal_terdaftar, urlfoto,
                  tb_owner_kost.kode_kost, tb_kost.nama_kost,
                  tb_kost.alamat, tb_kost.emailkost, tb_kost.logo

                  FROM tb_owner
                  
                  LEFT JOIN tb_owner_kost 
                  ON tb_owner.kode=tb_owner_kost.kode_owner
                  
                  LEFT JOIN tb_kost 
                  ON tb_owner_kost.kode_kost=tb_kost.kode_kost
                  
                  WHERE tb_owner.kode = '$kodeowner'";
                  
$resultgetowner = mysqli_query($koneksi, $querygetowner);
if( mysqli_num_rows($resultgetowner) > 0)
{
    while($rows = mysqli_fetch_assoc($resultgetowner))
    {
        $owner[] = array(
            "nama"          => $rows['nama'],
            "nomortelepon"  => $rows['nomor_telepon'],
            "email"         => $rows['email'],
            "tgldaftar"     => $rows['tanggal_terdaftar'],
            "urlfoto"       => $rows['urlfoto'],
            "kodekost"      => $rows['kode_kost'],
            "namakost"      => $rows['nama_kost'],
            "alamat"        => $rows['alamat'],
            "emailkost"     => $rows['emailkost'],
            "logo"          => $rows['logo'],
        );
    }
    
}
else
{
    $owner = null;
}

echo json_encode(array('owner' => $owner));

?>