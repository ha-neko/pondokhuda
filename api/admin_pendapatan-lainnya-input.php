<?php

include ("kon.php");
include ("admin_log.php");
include ("owner_log.php");

$keterangan = $_POST['_keterangan'];
$nominal = $_POST['_nominal'];
$metode = $_POST['_metode'];

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
$querygetkdbayar = "SELECT kode_pendapatan
                    FROM tb_pendapatan_lainnya
                    WHERE MONTH(tanggal)=MONTH(NOW())
                    AND YEAR(tanggal)=YEAR(NOW())
                    AND kode_kost='$kodekost'
                    ORDER BY kode_pendapatan DESC LIMIT 0,1";
       
$resultgetkdbayar = mysqli_query($koneksi, $querygetkdbayar);

if(mysqli_num_rows($resultgetkdbayar) > 0)
{
    $kode_pembayaran;
    while($rows = mysqli_fetch_assoc($resultgetkdbayar))
    {
        $kode_terakhir = $rows['kode_pendapatan'];
    }
    
    $kodependapatan = GenerateKodePendapatan($kode_terakhir, $kodekost);
}
else
{
    $kodependapatan = 'KW-PD-'.date('Y/m/').$kodekost.'/000001';
}

$tanggalbayar = date("Y-m-d");
$isInsert = false;

$queryinsertpendapatan = "INSERT INTO `tb_pendapatan_lainnya`

                           (`kode_pendapatan`, `tanggal`, `keterangan`,
                           `nominal`, `metode`, `kode_kost`)
                           
                           VALUES
                           ('$kodependapatan', '$tanggalbayar', '$keterangan',
                           '$nominal', '$metode', '$kodekost')";

$resultinsertpendapatan = mysqli_query($koneksi, $queryinsertpendapatan);

if($resultinsertpendapatan)
{
    $isInsert = true;
}
else
{
    $datapendapatan = 'data pembayaran gagal diinput, Error: '. mysqli_error($koneksi);
}

if($isInsert)
{
    if($metode == "Tunai")
    {
        $queryinsertjurnal = "INSERT INTO tb_keu_jurnal_umum
        
                              (kode_transaksi, kode_akun, tanggal,
                              keterangan, posisi, jumlah, kode_kost)
                              
                              VALUES
                              
                              ('$kodependapatan','1-1100','$tanggalbayar'
                              ,'Kas','Debit','$nominal', '$kodekost'),
                              
                              ('$kodependapatan','5-2300','$tanggalbayar'
                              ,'$keterangan','Kredit','$nominal', '$kodekost')";
    }
    else
    {
        $queryinsertjurnal = "INSERT INTO tb_keu_jurnal_umum
        
                              (kode_transaksi, kode_akun, tanggal,
                              keterangan, posisi, jumlah, kode_kost)
                              
                              VALUES
                              
                              ('$kodependapatan','1-1200','$tanggalbayar'
                              ,'Bank','Debit','$nominal', '$kodekost'),
                              
                              ('$kodependapatan','5-2300','$tanggalbayar'
                              ,'$keterangan','Kredit','$nominal', '$kodekost')";
    }
                               
    $resultinsertjurnal = mysqli_query($koneksi, $queryinsertjurnal);
    
    if($resultinsertjurnal)
    {
        if(isset($kodeadmin))
        {
            $log = CreateLogAdmin($koneksi, $namaadmin.' menginput pendapatan lainnya berupa pendapatan '.$keterangan.' sebesar '.$nominal, $kodeadmin, $kodekost);
        }
        
        if(isset($kodeowner))
        {
            $log = CreateLogOwner($koneksi, $namaowner.' menginput pendapatan lainnya berupa pendapatan '.$keterangan.' sebesar '.$nominal, $kodeowner, $kodekost);
        }
        
        $datapendapatan = 'data pembayaran berhasil diinput';
    }
    else
    {
        if(isset($kodeadmin))
        {
            $log = CreateLogAdmin($koneksi, $namaadmin.' menginput pendapatan lainnya berupa pendapatan '.$keterangan.' sebesar '.$nominal.' Error: '.mysqli_error($koneksi), $kodeadmin, $kodekost);
        }
        
        if(isset($kodeowner))
        {
            $log = CreateLogOwner($koneksi, $namaowner.' menginput pendapatan lainnya berupa pendapatan '.$keterangan.' sebesar '.$nominal.' Error: '.mysqli_error($koneksi), $kodeowner, $kodekost);
        }

        $datapendapatan = 'data laporan keuangan gagal diinput, Error: '. mysqli_error($koneksi);
    }
}

$hasilInputPendapatan = array('datapendapatan' => $datapendapatan);

echo json_encode($hasilInputPendapatan);

function GenerateKodePendapatan($kode, $kodekost)
{
    // kode = KW-thn/bln/nokamar/nourut (no urut = 6 digit)
    (int)$noUrutTerakhir = substr($kode, -6);
    
    $noUrutBaru =  $noUrutTerakhir + 1;
    
    $hasil = 'KW-PD-'.date('Y/m/').$kodekost.'/'.substr($noUrutTerakhir, 0, -1 * abs(strlen($noUrutBaru))).$noUrutBaru;
    
    return $hasil;
}

?>