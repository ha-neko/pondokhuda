<?php

include ("kon.php");
include ("admin_log.php");
include ("owner_log.php");

$keterangan = $_POST['_keterangan'];
$nominal = $_POST['_nominal'];
$metode = $_POST['_metode'];
$kodeakun = $_POST['_kodeakun'];

if(isset($_POST['_kodeadmin']))
{
    $kodeadmin = $_POST['_kodeadmin'];
    $namaadmin = $_POST['_namaadmin'];
}
if(isset($_POST['_kodeowner']))
{
    $kodeowner = $_POST['_kodeowner'];
    $namaowner = $_POST['_namaowner'];
}

$kodekost = $_POST['_kodekost'];

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if( !$koneksi)
{
    return;
}

// -------------------- Generate kodebayar --------------------------\
$querygetkdbayar = "SELECT kode_pengeluaran
                    FROM tb_pengeluaran
                    WHERE MONTH(tanggal)=MONTH(NOW())
                    AND YEAR(tanggal)=YEAR(NOW())
                    AND kode_kost='$kodekost'
                    ORDER BY kode_pengeluaran DESC LIMIT 0,1";
       
$resultgetkdbayar = mysqli_query($koneksi, $querygetkdbayar);

if(mysqli_num_rows($resultgetkdbayar) > 0)
{
    $kode_pembayaran;
    while($rows = mysqli_fetch_assoc($resultgetkdbayar))
    {
        $kode_terakhir = $rows['kode_pengeluaran'];
    }
    
    $kodepengeluaran = GenerateKodePengeluaran($kode_terakhir, $kodekost);
}
else
{
    $kodepengeluaran = 'KW-PG-'.date('Y/m/').$kodekost.'/000001';
}

$tanggalbayar = date("Y-m-d");
$isInsert = false;

$queryinsertpengeluaran = "INSERT INTO `tb_pengeluaran`(`kode_pengeluaran`, `tanggal`, `keterangan`, `nominal`, `metode`, `kode_kost`)
                           VALUES ('$kodepengeluaran', '$tanggalbayar', '$keterangan', '$nominal', '$metode', '$kodekost')";

$resultinsertpengeluaran = mysqli_query($koneksi, $queryinsertpengeluaran);

if($resultinsertpengeluaran)
{
    $isInsert = true;
}
else
{
    if(isset($kodeadmin))
    {
        $log = CreateLogAdmin($koneksi, $namaadmin.' menginput pengeluaran berupa pembayaran '.$keterangan.' sebesar '.$nominal.'. Error: '.mysqli_error($koneksi), $kodeadmin, $kodekost);
    }
    
    if(isset($kodeowner))
    {
        $log = CreateLogOwner($koneksi, $namaowner.' menginput pengeluaran berupa pembayaran '.$keterangan.' sebesar '.$nominal.'. Error: '.mysqli_error($koneksi), $kodeowner, $kodekost);
    }
    
    $datapengeluaran = 'data pembayaran gagal diinput, Error: '. mysqli_error($koneksi);
}

if($isInsert)
{
    if($metode == "Tunai")
    {
        $queryinsertjurnal = "INSERT INTO tb_keu_jurnal_umum
                              (kode_transaksi, kode_akun, tanggal,
                              keterangan, posisi, jumlah, kode_kost) VALUES
                              ('$kodepengeluaran','$kodeakun','$tanggalbayar','$keterangan','Debit','$nominal', '$kodekost'),
                              ('$kodepengeluaran','1-1100','$tanggalbayar','Kas','Kredit', '$nominal', '$kodekost')";
    }
    else
    {
        $queryinsertjurnal = "INSERT INTO tb_keu_jurnal_umum
                              (kode_transaksi, kode_akun, tanggal,
                              keterangan, posisi, jumlah, kode_kost) VALUES
                              ('$kodepengeluaran','$kodeakun','$tanggalbayar','$keterangan','Debit','$nominal', '$kodekost'),
                              ('$kodepengeluaran','1-1200','$tanggalbayar','Bank','Kredit','$nominal', '$kodekost')";
    }
                               
    $resultinsertjurnal = mysqli_query($koneksi, $queryinsertjurnal);
    
    if($resultinsertjurnal)
    {
        if(isset($kodeadmin))
        {
            $log = CreateLogAdmin($koneksi, $namaadmin.' menginput pengeluaran berupa pembayaran '.$keterangan.' sebesar '.$nominal, $kodeadmin, $kodekost);
        }
        
        if(isset($kodeowner))
        {
            $log = CreateLogOwner($koneksi, $namaowner.' menginput pengeluaran berupa pembayaran '.$keterangan.' sebesar '.$nominal, $kodeowner, $kodekost);
        }
        
        $datapengeluaran = 'data pembayaran berhasil diinput';
    }
    else
    {
        if(isset($kodeadmin))
        {
            $log = CreateLogAdmin($koneksi, $namaadmin.' menginput pengeluaran berupa pembayaran '.$keterangan.' sebesar '.$nominal.'. Error: '.mysqli_error($koneksi), $kodeadmin, $kodekost);
        }
        
        if(isset($kodeowner))
        {
            $log = CreateLogOwner($koneksi, $namaowner.' menginput pengeluaran berupa pembayaran '.$keterangan.' sebesar '.$nominal.'. Error: '.mysqli_error($koneksi), $kodeowner, $kodekost);
        }
        
        $datapengeluaran = 'data laporan keuangan gagal diinput, Error: '. mysqli_error($koneksi);
    }
}

$hasilInputPengeluaran = array('datapengeluaran' => $datapengeluaran);

echo json_encode($hasilInputPengeluaran);

function GenerateKodePengeluaran($kode, $kodekost)
{
    // kode = KW-thn/bln/nokamar/nourut (no urut = 6 digit)
    (int)$noUrutTerakhir = substr($kode, -6);
    
    $noUrutBaru =  $noUrutTerakhir + 1;
    
    $hasil = 'KW-PG-'.date('Y/m/').$kodekost.'/'.substr($noUrutTerakhir, 0, -1 * abs(strlen($noUrutBaru))).$noUrutBaru;
    
    return $hasil;
}

?>