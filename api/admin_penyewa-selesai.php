<?php
include('kon.php');
include ("admin_log.php");
include ("owner_log.php");

$kodesewa = $_POST['_kodesewa'];
$kodepenyewa = $_POST['_kodepenyewa'];
$namapenyewa = $_POST['_namapenyewa'];

$kodekamarlama = $_POST['_kodekamar'];
$nokamar = $_POST['_nokamar'];
$hargakamarlama = $_POST['_hargakamar'];
$tglpembayaranlama = $_POST['_tglpembayaran'];
$periodebayarlama = $_POST['_periode'];

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

if (!$koneksi)
{
    header('Content-Type: application/json');
    echo json_encode(array('selesaisewa' => 'koneksi database gagal'));
    return;
}

date_default_timezone_set("Asia/Bangkok");
// --------------------------------------------------------------------\\

$tanggalSelesai = date("Y-m-d");
$tanggalMulai = date("Y-m-d");

$queryGetPenyewaPindah = "SELECT kode_penyewa FROM tb_penyewa_kamar
                          WHERE kode_sewa='$kodesewa'";

$resultGetPenyewaPindah = mysqli_query($koneksi, $queryGetPenyewaPindah);

if (mysqli_num_rows($resultGetPenyewaPindah) > 0)
{
    // ------- penyewanya dua -------\
    if (mysqli_num_rows($resultGetPenyewaPindah) > 1)
    {
        // ------- yang ikut satu -------\
        if (count($kodepenyewa) == 1)
        {
            // ----------- Get Penyewa Pindah -----------\
            foreach ($kodepenyewa as $penyewa)
            {
                if (isset($penyewa))
                {
                    $penyewaberes = $penyewa;
                }
            }
            
            $queryInsertPindah = "INSERT INTO tb_pindah_kamar
                                  (kode_sewa, tanggal_pindah)
                                  VALUES ('$kodesewa', '$tanggalSelesai')";

            $resultInsertPindah = mysqli_query($koneksi, $queryInsertPindah);
            
            if($resultInsertPindah)
            {
                $kodepindah = mysqli_insert_id($koneksi);
                
                $queryInsertSewaAsal = "
                
                INSERT INTO
                
                tb_sewa_kamar
                
                (kode_kamar, tanggal_mulai, harga_perbulan, 
                tanggal_pembayaran, periode_bayar)
                
                VALUES ('$kodekamarlama', '$tanggalMulai',
                $hargakamarlama - 200000, '$tglpembayaranlama',
                '$periodebayarlama')";

                $resultInsertSewaAsal = mysqli_query($koneksi, $queryInsertSewaAsal);
    
                if ($resultInsertSewaAsal)
                {
                    $kodeSewaGakSelesai = mysqli_insert_id($koneksi);
    
                    $queryInsertPenyewaGakPindah = 
                    "INSERT INTO tb_penyewa_kamar
                    (kode_sewa, kode_penyewa, kode_pindah)
                    
                    SELECT '$kodeSewaGakSelesai', kode_penyewa, '$kodepindah'
                    
                    FROM tb_penyewa_kamar WHERE kode_sewa='$kodesewa'
                    
                    AND kode_penyewa !='$penyewaberes'";
                    
                    $resultInsertPenyewaGakPindah = mysqli_query($koneksi, $queryInsertPenyewaGakPindah);
    
                    if ($resultInsertPenyewaGakPindah)
                    {
                        // ------- selesai sewa -------\
                        $querySelesai = "UPDATE tb_sewa_kamar
                                         SET tanggal_selesai='$tanggalSelesai'
                                         WHERE kode_sewa='$kodesewa'";
                
                        $resultSelesai = mysqli_query($koneksi, $querySelesai);
                
                        if ($resultSelesai)
                        {
                            $penyewaselesai = "penyewa selesai menyewa";
                        }
                        else
                        {
                            $penyewaselesai = "Error penyewa selesai: ".mysqli_error($koneksi);
                        }
                    }
                    else
                    {
                        $penyewaselesai = "Error penyewa gak selesai: ".mysqli_error($koneksi);
                    }
                }
                else
                {
                    $penyewaselesai = "Error sewa gak selesai: ".mysqli_error($koneksi);
                }
            }
            else
            {
                $penyewaselesai = "Error pindah: ".mysqli_error($koneksi);
            }
        }
        // ------- semua pindah -------\
        else
        {
            // ------- selesai sewa -------\
            $querySelesai = "UPDATE tb_sewa_kamar
                             SET tanggal_selesai='$tanggalSelesai'
                             WHERE kode_sewa='$kodesewa'";
    
            $resultSelesai = mysqli_query($koneksi, $querySelesai);
    
            if ($resultSelesai)
            {
                $penyewaselesai = "penyewa selesai menyewa";
            }
            else
            {
                $penyewaselesai = "Error penyewa selesai: ".mysqli_error($koneksi);
            }
        } 
    }
    // ------- penyewanya satu -------\
    else
    {
        // ------- selesai sewa -------\
        $querySelesai = "UPDATE tb_sewa_kamar
                         SET tanggal_selesai='$tanggalSelesai'
                         WHERE kode_sewa='$kodesewa'";

        $resultSelesai = mysqli_query($koneksi, $querySelesai);

        if ($resultSelesai)
        {
            $penyewaselesai = "penyewa selesai menyewa";
        }
        else
        {
            $penyewaselesai = "Error penyewa selesai: ".mysqli_error($koneksi);
        }
    }
}
else
{
    $penyewaselesai = "Error get penyewa pindah: ".mysqli_error($koneksi);
}

if (count($kodepenyewa) == 1)
{
    foreach ($namapenyewa as $penyewa)
    {
        if (isset($penyewa))
        {
            $penyewaberes = $penyewa;
        }
    }
    
    if(isset($kodeadmin))
    {
        $log = CreateLogAdmin($koneksi, $namaadmin.' menghentikan sewa kost penyewa di kamar '.$nokamar.' atas nama penyewa '.$penyewaberes, $kodeadmin, $kodekost);
    }
    
    if(isset($kodeowner))
    {
        $log = CreateLogOwner($koneksi, $namaowner.' menghentikan sewa kost penyewa di kamar '.$nokamar.' atas nama penyewa '.$penyewaberes, $kodeowner, $kodekost);
    }
}
else
{
    if(isset($kodeadmin))
    {
        $log = CreateLogAdmin($koneksi, $namaadmin.' menghentikan sewa kost penyewa di kamar '.$nokamar.' atas nama penyewa '.$namapenyewa[0].', '.$namapenyewa[1], $kodeadmin, $kodekost);
    }
    
    if(isset($kodeowner))
    {
        $log = CreateLogOwner($koneksi, $namaowner.' menghentikan sewa kost penyewa di kamar '.$nokamar.' atas nama penyewa '.$namapenyewa[0].', '.$namapenyewa[1], $kodeowner, $kodekost);
    }
}

echo json_encode(array('selesaisewa' => $penyewaselesai));
?>