<?php
include('kon.php');

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if( !$koneksi)
{
    return;
}

// ---------------------- JUMLAH OWNER ---------------------- \\
$qryOwner = "SELECT COUNT(kode) AS jml_owner
            FROM tb_owner";
                   
$rslOwner = mysqli_query($koneksi, $qryOwner);
if( mysqli_num_rows($rslOwner) == 1)
{
    while($rows = mysqli_fetch_assoc($rslOwner)) 
    {
        $jmlowner =  $rows['jml_owner'];
    }
}
// ---------------------- END OF JUMLAH OWNER ---------------------- \\

// ---------------------- JUMLAH KOST ---------------------- \\
$qryKost = "SELECT COUNT(kode_kost) AS jml_kost
            FROM tb_kost";
                   
$rslKost = mysqli_query($koneksi, $qryKost);
if( mysqli_num_rows($rslKost) == 1)
{
    while($rows = mysqli_fetch_assoc($rslKost)) 
    {
        $jmlkost =  $rows['jml_kost'];
    }
}
// ---------------------- END OF JUMLAH KOST ---------------------- \\

// ---------------------- JUMLAH KAMAR ---------------------- \\
$qryKamar = "SELECT COUNT(id) AS jml_kamar
            FROM tb_kamar";
                   
$rslKamar = mysqli_query($koneksi, $qryKamar);
if( mysqli_num_rows($rslKamar) == 1)
{
    while($rows = mysqli_fetch_assoc($rslKamar)) 
    {
        $jmlkamar = $rows['jml_kamar'];
    }
}
// ---------------------- END OF JUMLAH KAMAR ---------------------- \\

// ---------------------- JUMLAH PENYEWA ---------------------- \\
$qryPenyewa = "SELECT COUNT(kode) AS jml_penyewa
            FROM tb_penyewa";
                   
$rslPenyewa = mysqli_query($koneksi, $qryPenyewa);
if( mysqli_num_rows($rslPenyewa) == 1)
{
    while($rows = mysqli_fetch_assoc($rslPenyewa)) 
    {
        $jmlpenyewa = $rows['jml_penyewa'];
    }
}
// ---------------------- END OF JUMLAH PENYEWA ---------------------- \\

$report[] = array(
    'jumlah_owner' => $jmlowner,
    'jumlah_kost' => $jmlkost,
    'jumlah_kamar' => $jmlkamar,
    'jumlah_penyewa' => $jmlpenyewa
    );

echo json_encode(array('report' => $report));

?>