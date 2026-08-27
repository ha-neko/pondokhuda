<?php
include('kon.php');
include ("admin_log.php");
include ("owner_log.php");

$du = $_POST['_du'];
$kode = $_POST['_kode'];    // Read ONLY
// $noktp = $_POST['_noktp'];
// $nokamar = $_POST['_nokamar'];
$nama = $_POST['_nama'];
$tgllahir = $_POST['_tgllahir'];

$jk = $_POST['_jk'];
$nohp = $_POST['_nohp'];
$email = $_POST['_email'];
$foto = $_POST['_foto'];
$namaortu = $_POST['_namaortu'];

$nohportu = $_POST['_nohportu'];
$alamat = $_POST['_alamat'];
$kelurahan = $_POST['_kelurahan'];
$kecamatan = $_POST['_kecamatan'];
$kotakab = $_POST['_kotakab'];
$provinsi = $_POST['_provinsi'];

$kodepos = $_POST['_kodepos'];
$tempatkuliahkerja = $_POST['_tempatkuliahkerja'];
$jurkuliah = $_POST['_jurkuliah'];
// $periodebayar = $_POST['_periode'];
$status = $_POST['_status'];
// $pin = $_POST['_pin'];

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

$USER_IMAGE_FOLDER = "/Assets/images/user/";
$koneksi = mysqli_connect($host, $user, $pass, $daba);

if( !$koneksi)
{
	header('Content-Type: application/json');
	echo json_encode(array('daftarpenyewa' => 'koneksi database gagal'));
	return;
}

date_default_timezone_set("Asia/Bangkok");
$inputDate = date("Y-m-d H:i:s");
if($du == "on")
{
    if($foto == "0")
    {
        $query = "UPDATE tb_penyewa SET nama='$nama',tgllahir='$tgllahir',jk='$jk',nohp='$nohp',email='$email',namaortu='$namaortu',nohportu='$nohportu',alamat='$alamat',kelurahan='$kelurahan',kecamatan='$kecamatan',kotakab='$kotakab',provinsi='$provinsi',kodepos='$kodepos',tempatkuliahkerja='$tempatkuliahkerja',jurusankuliah='$jurkuliah',status='$status' WHERE kode='$kode'";
    }
    else
    {
        $urlfoto = $USER_IMAGE_FOLDER . $kode . '.jpg';
        $query = "UPDATE tb_penyewa SET nama='$nama',tgllahir='$tgllahir',jk='$jk',nohp='$nohp',email='$email', urlfoto='$urlfoto',namaortu='$namaortu',nohportu='$nohportu',alamat='$alamat',kelurahan='$kelurahan',kecamatan='$kecamatan',kotakab='$kotakab',provinsi='$provinsi',kodepos='$kodepos',tempatkuliahkerja='$tempatkuliahkerja',jurusankuliah='$jurkuliah',status='$status' WHERE kode='$kode'";
        
        file_put_contents("/home/pondokhu/public_html/Assets/images/user/" . $kode . ".jpg",base64_decode($foto));
    }
}
else 
{
    if($foto == "0")
    {
        $query = "UPDATE tb_penyewa SET nama='$nama',tgllahir='$tgllahir',jk='$jk',nohp='$nohp',email='$email',namaortu='$namaortu',nohportu='$nohportu',alamat='$alamat',tempatkuliahkerja='$tempatkuliahkerja',jurusankuliah='$jurkuliah',status='$status' WHERE kode='$kode'";
    }
    else
    {
        $urlfoto = $USER_IMAGE_FOLDER . $kode . '.jpg';
        $query = "UPDATE tb_penyewa SET nama='$nama',tgllahir='$tgllahir',jk='$jk',nohp='$nohp',email='$email', urlfoto='$urlfoto',namaortu='$namaortu',nohportu='$nohportu',alamat='$alamat',tempatkuliahkerja='$tempatkuliahkerja',jurusankuliah='$jurkuliah',status='$status' WHERE kode='$kode'";
        
        file_put_contents("/home/pondokhu/public_html/Assets/images/user/" . $kode . ".jpg",base64_decode($foto));
    }
}


// echo "q: " . $query;
$result = mysqli_query($koneksi, $query);

if( $result)
{
    if(isset($kodeadmin))
    {
        $log = CreateLogAdmin($koneksi, $namaadmin.' mengganti data penyewa atas nama penyewa '.$nama, $kodeadmin, $kodekost);
    }
    
    if(isset($kodeowner))
    {
        $log = CreateLogOwner($koneksi, $namaowner.' mengganti data penyewa atas nama penyewa '.$nama, $kodeowner, $kodekost);
    }
    echo json_encode(array("daftarpenyewa" => "berhasil update penyewa " . $foto));
}
else
{
    if(isset($kodeadmin))
    {
        $log = CreateLogAdmin($koneksi, $namaadmin.' mengganti data penyewa atas nama penyewa '.$nama.'. Error: '.mysqli_error($koneksi), $kodeadmin, $kodekost);
    }
    
    if(isset($kodeowner))
    {
        $log = CreateLogOwner($koneksi, $namaowner.' mengganti data penyewa atas nama penyewa '.$nama.'. Error: '.mysqli_error($koneksi), $kodeowner, $kodekost);
    }
    echo json_encode(array("daftarpenyewa" => "error update penyewa"));
}
?>