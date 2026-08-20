<?php

include ("kon.php");
include("owner_log.php");

$kodekamar = $_POST['_kodekamar'];
$nokamar = $_POST['_nokamar'];
$statuskamar = $_POST['_statuskamar'];
$harga = $_POST['_hargakamar'];
$hargadisewakan = $_POST['_hargadisewakan'];
$kodeowner = $_POST['_kodeowner'];
$namaowner = $_POST['_namaowner'];
$kodekost = $_POST['_kodekost'];

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if( !$koneksi)
{
    return;
}

$datakamar = array();

$queryupdatekamar = "UPDATE tb_kamar SET nokamar = '$nokamar', statuskamar = '$statuskamar', harga = $harga WHERE id = '$kodekamar'";
$resultupdatekamar = mysqli_query($koneksi, $queryupdatekamar);

if($resultupdatekamar)
{
    if($hargadisewakan != "")
    {
        $queryupdatekamar = "UPDATE tb_sewa_kamar
        
                            INNER JOIN tb_kamar
                            ON tb_sewa_kamar.kode_kamar = tb_kamar.id
                            
                            SET tb_sewa_kamar.harga_perbulan='$hargadisewakan'
                            
                            WHERE tb_sewa_kamar.kode_kamar = '$kodekamar'
                            AND tb_sewa_kamar.tanggal_selesai IS NULL
                            AND tb_kamar.kode_kost='$kodekost'";
        
        $resultupdatekamar = mysqli_query($koneksi, $queryupdatekamar);
        if($resultupdatekamar)
        {
            $log = CreateLogOwner($koneksi, $namaowner.' mengubah data kamar dengan nomor kamar '.$nokamar, $kodeowner, $kodekost);
            echo json_encode(array('datakamar' => 'data berhasil diubah'));
        }
        else
        {
            $log = CreateLogOwner($koneksi, $namaowner.' mengubah data kamar dengan nomor kamar '.$nokamar.'Error: '.mysqli_error($koneksi), $kodeowner, $kodekost);
            echo json_encode(array('datakamar' => 'data gagal diubah, Error: '. mysqli_error($koneksi)));
        }
    }
    else
    {
        $log = CreateLogOwner($koneksi, $namaowner.' mengubah data kamar dengan nomor kamar '.$nokamar, $kodeowner, $kodekost);
        echo json_encode(array('datakamar' => 'data berhasil diubah'));
    }
}
else
{
    $log = CreateLogOwner($koneksi, $namaowner.' mengubah data kamar dengan nomor kamar '.$nokamar.'Error: '.mysqli_error($koneksi), $kodeowner, $kodekost);
    echo json_encode(array('datakamar' => 'data gagal diubah, Error: '. mysqli_error($koneksi)));
}

?>