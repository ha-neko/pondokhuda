<?php
include('kon.php');

$kodekost = $_POST['_kodekost'];

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if (!$koneksi)
{
    header('Content-Type: application/json');
    echo json_encode(array('error' => 'koneksi database gagal'));
    return;
}

// get data semua penyewa -----------------------------------------
$querygetallpenyewa = "SELECT kode, noktp, tb_kamar.nokamar, nama, tgllahir, jk,
                      nohp, email, urlfoto, namaortu, nohportu, alamat, kelurahan,
                      kecamatan, kotakab, provinsi, kodepos, tempatkuliahkerja,
                      jurusankuliah, tb_sewa_kamar.periode_bayar, status, nomorpin
                      FROM tb_penyewa
                      INNER JOIN tb_penyewa_kamar ON tb_penyewa.kode=tb_penyewa_kamar.kode_penyewa
                      INNER JOIN tb_sewa_kamar ON tb_penyewa_kamar.kode_sewa=tb_sewa_kamar.kode_sewa
                      INNER JOIN tb_kamar ON tb_sewa_kamar.kode_kamar=tb_kamar.id
                      WHERE tb_sewa_kamar.tanggal_selesai IS NULL
                      AND tb_penyewa.kode_kost='$kodekost'";
                      
$resultgetallpenyewa = mysqli_query($koneksi, $querygetallpenyewa);
if( mysqli_num_rows($resultgetallpenyewa) > 0)
{
    while($rows = mysqli_fetch_assoc($resultgetallpenyewa))
    {
        $datapenyewa[] = array(
            
            "kode" => $rows['kode'],
            "noktp" => $rows['noktp'],
            "nokamar" => $rows['nokamar'],
            "nama" => $rows['nama'],
            "tgllahir" => $rows['tgllahir'],
            
            "jk" => $rows['jk'],
            "hp" => $rows['nohp'],
            "email" => $rows['email'],
            "urlfoto" => $rows['urlfoto'],
            "namaortu" => $rows['namaortu'],
            
            "hportu" => $rows['nohportu'],
            "alamat" => $rows['alamat'],
            "kelurahan" => $rows['kelurahan'],
            "kecamatan" => $rows['kecamatan'],
            "kotakab" => $rows['kotakab'],
            "provinsi" => $rows['provinsi'],
            
            "kodepos" => $rows['kodepos'],
            "tempatkuliahkerja" => $rows['tempatkuliahkerja'],
            "jurusankul" => $rows['jurusankuliah'],
            "periodebay" => $rows['periode_bayar'],
            "status" => $rows['status'],
            
            "pin" => $rows['nomorpin']
            );
    }
    
}
else
{
    $datapenyewa = null;
}

$hasil = json_encode(array('datapenyewa' => $datapenyewa));

echo $hasil
?>