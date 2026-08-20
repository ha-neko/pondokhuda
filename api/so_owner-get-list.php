<?php

include ("kon.php");

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if( !$koneksi)
{
    return;
}

$admin = array();

// get data semua kamar -----------------------------------------
$querygetowner = "SELECT kode, nama, pin, nomor_telepon, email,
                  DATE_FORMAT(tanggal_terdaftar,'%d/%m/%Y')
                  AS tanggal_terdaftar,
                  tb_owner_kost.kode_kost, tb_kost.nama_kost

                  FROM tb_owner
                  
                  LEFT JOIN tb_owner_kost 
                  ON tb_owner.kode=tb_owner_kost.kode_owner
                  
                  LEFT JOIN tb_kost 
                  ON tb_owner_kost.kode_kost=tb_kost.kode_kost
                  
                  ORDER BY tb_owner.kode ASC";
                  
$resultgetowner = mysqli_query($koneksi, $querygetowner);
if( mysqli_num_rows($resultgetowner) > 0)
{
    while($rows = mysqli_fetch_assoc($resultgetowner))
    {
        $owner[] = array(
            "kode"          => $rows['kode'],
            "nama"          => $rows['nama'],
            "email"         => $rows['email'],
            "nomortelepon"  => $rows['nomor_telepon'],
            "tgldaftar"     => $rows['tanggal_terdaftar'],
            "kodekost"      => $rows['kode_kost'],
            "namakost"      => $rows['nama_kost'],
        );
    }
    
}
else
{
    $owner = null;
}

echo json_encode(array('owner' => $owner));

?>