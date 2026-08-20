<?php

include ('kon.php');

$kode = $_POST['kode'];
$pin = $_POST['pin'];

// $kode = "000001";
// $pin = '123g456';

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if( !$koneksi)
{
	return;
}

// ambil kontak admin untuk whatsapp
$kontakadmin = array();
$querykontakadmin = "SELECT
        	tb_admin.nama,
            tb_admin.nomor_telepon
        FROM
        	tb_penyewa
        INNER JOIN tb_admin_kost ON tb_admin_kost.kode_kost = tb_penyewa.kode_kost
        INNER JOIN tb_admin ON tb_admin.kode = tb_admin_kost.kode_admin
        WHERE 
        	tb_penyewa.kode = '$kode' AND tb_penyewa.nomorpin = '$pin'";
$resultkontakadmin = mysqli_query($koneksi, $querykontakadmin);
if( mysqli_num_rows($resultkontakadmin))
{
    while($rowskontakadmin = mysqli_fetch_assoc($resultkontakadmin))
    {
        $kontakadmin[] = array(
            "nama" => $rowskontakadmin['nama'],
            "wasap" => $rowskontakadmin['nomor_telepon'],
            "status" => "Admin"
            );
    }
}

// ambil kontak owner untuk whatsapp
$kontakowner = array();
$querykontakowner = "SELECT
        	tb_owner.nama,
            tb_owner.nomor_telepon
        FROM
        	tb_penyewa
        INNER JOIN tb_owner_kost ON tb_owner_kost.kode_kost = tb_penyewa.kode_kost
        INNER JOIN tb_owner ON tb_owner.kode = tb_owner_kost.kode_owner
        WHERE 
        	tb_penyewa.kode = '$kode' AND tb_penyewa.nomorpin = '$pin'";
$resultkontakowner = mysqli_query($koneksi, $querykontakowner);
if( mysqli_num_rows($resultkontakowner))
{
    while($rowskontakowner = mysqli_fetch_assoc($resultkontakowner))
    {
        $kontakowner[] = array(
            "nama" => $rowskontakowner['nama'],
            "wasap" => $rowskontakowner['nomor_telepon'],
            "status" => "Owner"
            );
    }
}

echo json_encode(array('kontakadmin' =>$kontakadmin, 'kontakowner' => $kontakowner));

?>