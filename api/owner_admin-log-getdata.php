<?php

include ("kon.php");

$kodeowner = $_POST['_kodeowner'];
$kdoekost = $_POST['_kodekost'];

$koneksi = mysqli_connect($host, $user, $pass, $daba);

$kodekost = $_POST['_kodekost'];

if (!$koneksi)
{
    header('Content-Type: application/json');
    echo json_encode(array('logadmin' => 'koneksi database gagal'));
    return;
}

$query = "SELECT DATE_FORMAT(waktu_akses, '%d/%m/%Y %T') AS waktu_akses, tb_admin.nama, aktifitas
                    FROM tb_log_admin
                    INNER JOIN tb_admin
                    ON tb_log_admin.kode_admin=tb_admin.kode
                    WHERE tb_admin.kode_owner='$kodeowner'
                    AND (tb_log_admin.kode_kost='$kodekost'
                    OR tb_log_admin.kode_kost IS NULL)
                    ORDER BY waktu_akses DESC";
                    
$result = mysqli_query($koneksi, $query);
if( mysqli_num_rows($result) > 0)
{
    while($rows = mysqli_fetch_assoc($result))
    {
        $logadmin[] = array(
            "waktu_akses"   => $rows['waktu_akses'],
            "nama"          => $rows['nama'],
            "aktifitas"     => $rows['aktifitas'],
        );
    }
    
}
else
{
    $logadmin = null;
}

echo json_encode(array('logadmin' => $logadmin));

?>