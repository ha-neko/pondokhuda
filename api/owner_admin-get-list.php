<?php

include ("kon.php");

$kode = $_POST['_kode'];

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if( !$koneksi)
{
    return;
}

$admin = array();

// get data semua kamar -----------------------------------------
$querygetadmin = "SELECT kode, nama, pin, nomor_telepon,
                  tb_admin_kost.kode_kost, tb_kost.nama_kost

                  FROM tb_admin
                  
                  LEFT JOIN tb_admin_kost 
                  ON tb_admin.kode=tb_admin_kost.kode_admin
                  
                  LEFT JOIN tb_kost 
                  ON tb_admin_kost.kode_kost=tb_kost.kode_kost
                  
                  WHERE tb_admin.kode_owner='$kode'
                  ORDER BY tb_admin.kode";
                    
$resultgetadmin = mysqli_query($koneksi, $querygetadmin);
if( mysqli_num_rows($resultgetadmin) > 0)
{
    while($rows = mysqli_fetch_assoc($resultgetadmin))
    {
        $admin[] = array(
            "kode"          => $rows['kode'],
            "nama"          => $rows['nama'],
            "pin"           => $rows['pin'],
            "nomortelepon"  => $rows['nomor_telepon'],
            "kodekost"      => $rows['kode_kost'],
            "namakost"      => $rows['nama_kost'],
        );
    }
    
}
else
{
    $admin = null;
}

echo json_encode(array('admin' => $admin));

?>