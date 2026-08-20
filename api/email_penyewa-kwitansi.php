<?php

include ("kon.php");

$koneksi = mysqli_connect($host, $user, $pass, $daba);

$kodepenyewa = $_POST['_kode'];

if( !$koneksi)
{
    return;
}

$queryInformasisewa = "SELECT tb_sewa_kamar.kode_kamar, tb_penyewa.kode_kost, tb_bayar_kost.periode_bayar
                       FROM tb_penyewa
                       INNER JOIN tb_penyewa_kamar ON tb_penyewa.kode=tb_penyewa_kamar.kode_penyewa
                       INNER JOIN (SELECT * FROM tb_sewa_kamar WHERE tanggal_selesai IS NULL) tb_sewa_kamar ON tb_penyewa_kamar.kode_sewa=tb_sewa_kamar.kode_sewa
                       INNER JOIN tb_bayar_kost ON tb_sewa_kamar.kode_sewa=tb_bayar_kost.kode_sewa
                       WHERE tb_penyewa.kode='$kodepenyewa'
                       AND tb_bayar_kost.kode_bayar IN (SELECT max(tb_bayar_kost.kode_bayar) FROM tb_bayar_kost GROUP BY kode_sewa)";

$resultInformasiSewa = mysqli_query($koneksi, $queryInformasisewa);
if($resultInformasiSewa)
{
    while($rows = mysqli_fetch_assoc($resultInformasiSewa))
    {
        $kodekamar = $rows['kode_kamar'];
        $kodekost = $rows['kode_kost'];
        $periodebayar = $rows['periode_bayar'];
    }
}
else
{
    echo json_encode(array("kwitansi" => "error query informasi sewa")); 
    exit;
}

$namaList = array();
$emailList = array();
$nokw = "";
$uang = "";
$periodkwitansi = "";
$period = "";
$denda = "";
$diskon = "";
$totalbayar = "";
$totalharga = "";
$totalbayarsebelumnya = "";
$sisabayar = "";
$hargaperbulan = "";
$metode = "";
$tgls = "";
$logo = "";
$namakost = "";
$status = "";

$cekemail = "";
$namaEmail = "";
$c = true;

    $queryGetEmailPenyewa = "SELECT 
tb_sewa_kamar.kode_sewa, 
   tb_bayar_kost.kode_bayar, 
tb_penyewa.nama, 
tb_penyewa.email, 
tb_bayar_kost.harga_perbulan,
tb_bayar_kost.periode_bayar,
tb_bayar_kost.denda,
tb_bayar_kost.diskon,
tb_bayar_kost.total_bayar,
tb_bayar_kost.total_bayar_sebelumnya,
tb_bayar_kost.sisa_bayar_sebelumnya,
tb_bayar_kost.total_harga,
tb_bayar_kost.metode,

IF(
(SELECT COUNT(*) FROM tb_bayar_kost WHERE tb_bayar_kost.kode_sewa = (SELECT tb_sewa_kamar.kode_sewa FROM tb_sewa_kamar WHERE tb_sewa_kamar.kode_kamar = '$kodekamar' AND tb_sewa_kamar.tanggal_selesai IS NULL LIMIT 0,1)) = 1,

CONCAT('Pembayaran pertama periode sewa ',
       
DATE_FORMAT((SELECT tb_bayar_kost.tanggal_pembayaran FROM tb_bayar_kost 
WHERE tb_bayar_kost.kode_sewa = (SELECT tb_sewa_kamar.kode_sewa FROM tb_sewa_kamar 
WHERE tb_sewa_kamar.kode_kamar = '$kodekamar' AND tb_sewa_kamar.tanggal_selesai IS NULL LIMIT 0,1)
ORDER BY tb_bayar_kost.kode_bayar DESC LIMIT 0,1),'%d/%m/%Y')
       
,' s.d. ',
DATE_FORMAT(DATE_ADD((SELECT tb_bayar_kost.tanggal_pembayaran FROM tb_bayar_kost 
WHERE tb_bayar_kost.kode_sewa = (SELECT tb_sewa_kamar.kode_sewa FROM tb_sewa_kamar 
WHERE tb_sewa_kamar.kode_kamar = '$kodekamar' AND tb_sewa_kamar.tanggal_selesai IS NULL LIMIT 0,1) 
ORDER BY tb_bayar_kost.kode_bayar DESC LIMIT 0,1), INTERVAL $periodebayar MONTH),'%d/%m/%Y')),

CONCAT(DATE_FORMAT((SELECT tb_bayar_kost.tanggal_pembayaran FROM tb_bayar_kost 
WHERE tb_bayar_kost.kode_sewa = (SELECT tb_sewa_kamar.kode_sewa FROM tb_sewa_kamar 
WHERE tb_sewa_kamar.kode_kamar = '$kodekamar' AND tb_sewa_kamar.tanggal_selesai IS NULL LIMIT 0,1)
ORDER BY tb_bayar_kost.kode_bayar DESC LIMIT 0,1),'%d/%m/%Y')
       
,' s.d. ',
DATE_FORMAT(DATE_ADD((SELECT tb_bayar_kost.tanggal_pembayaran FROM tb_bayar_kost 
WHERE tb_bayar_kost.kode_sewa = (SELECT tb_sewa_kamar.kode_sewa FROM tb_sewa_kamar 
WHERE tb_sewa_kamar.kode_kamar = '$kodekamar' AND tb_sewa_kamar.tanggal_selesai IS NULL LIMIT 0,1) 
ORDER BY tb_bayar_kost.kode_bayar DESC LIMIT 0,1), INTERVAL $periodebayar MONTH),'%d/%m/%Y'))    
) AS periodebayarkuitansi,

(SELECT (tb_bayar_kost.total_harga - (tb_bayar_kost.total_bayar + tb_bayar_kost.total_bayar_sebelumnya)) FROM tb_bayar_kost WHERE tb_bayar_kost.kode_sewa = (SELECT tb_sewa_kamar.kode_sewa FROM tb_sewa_kamar WHERE tb_sewa_kamar.kode_kamar = '$kodekamar' AND tb_sewa_kamar.tanggal_selesai IS NULL LIMIT 0,1) ORDER BY tb_bayar_kost.kode_bayar DESC LIMIT 0,1) AS sisabayarsaatini,

tb_bayar_kost.tanggal_bayar, 
tb_kost.nama_kost, 
tb_kost.logo, 
tbv_status_bayar.status_bayar

FROM tb_sewa_kamar 

INNER JOIN tb_penyewa_kamar ON tb_sewa_kamar.kode_sewa = tb_penyewa_kamar.kode_sewa
INNER JOIN tb_penyewa ON tb_penyewa_kamar.kode_penyewa = tb_penyewa.kode
INNER JOIN tb_bayar_kost ON tb_penyewa_kamar.kode_sewa = tb_bayar_kost.kode_sewa
INNER JOIN tb_kost ON tb_penyewa.kode_kost = tb_kost.kode_kost
INNER JOIN tbv_status_bayar ON tb_sewa_kamar.kode_sewa = tbv_status_bayar.kode_sewa

WHERE tb_sewa_kamar.tanggal_selesai IS NULL
AND tb_bayar_kost.kode_bayar IN (SELECT max(tb_bayar_kost.kode_bayar) FROM tb_bayar_kost
INNER JOIN tb_sewa_kamar ON tb_bayar_kost.kode_sewa = tb_sewa_kamar.kode_sewa
                                         WHERE tb_sewa_kamar.kode_kamar = '$kodekamar' 
                                         AND tb_sewa_kamar.tanggal_selesai IS NULL
                                         GROUP BY tb_sewa_kamar.kode_sewa)
                                         AND tb_sewa_kamar.kode_kamar = '$kodekamar' AND
                                         tb_penyewa.kode_kost = '$kodekost'
                                         GROUP BY tb_penyewa_kamar.kode_penyewa";


$resultGetEmail = mysqli_query($koneksi, $queryGetEmailPenyewa);
if($resultGetEmail)
{
    while($rows = mysqli_fetch_assoc($resultGetEmail))
    {
        array_push($namaList, $rows['nama']);
        array_push($emailList, $rows['email']);
        $nokw = $rows['kode_bayar'];
        $uang = $rows['total_bayar'];
        // $period = $periodebayar;
        $periodkwitansi = $rows['periodebayarkuitansi'];    // tambahan
        $period = $rows['periode_bayar'];
        $denda = $rows['denda'];
        $diskon = $rows['diskon'];
        $totalbayar = $rows['total_bayar'];
        $totalharga = $rows['total_harga'];
        $totalbayarsebelumnya = $rows['total_bayar_sebelumnya'];
        $sisabayarsebelumnya = $rows['sisa_bayar_sebelumnya'];
        $metode = $rows['metode'];
        $sisabayar = (int)$totalharga - (int)$totalbayar;
        if($sisabayar < 0)
        {
            $sisabayar = 0;
        }
        $hargaperbulan = $rows['harga_perbulan'];
        $sisasaatini = $rows['sisabayarsaatini']; // tambahan
        $tgls = $rows['tanggal_bayar'];
        $logo = "https://pondok-huda.com" . $rows['logo'];
        $namakost = $rows['nama_kost'];
        if($rows['status_bayar'] == "Lunas")
        {
            $status = "Lunas";
        }
        else
        {
            $status = "Belum Lunas";
        }
    }
}
else {
    echo json_encode(array("kwitansi" => "error query email"));
    exit;
}

for($i = 0; count($namaList) > $i; $i++)
{
    if($namaEmail == "")
    {
        $namaEmail = $namaList[$i];
    }
    else
    {
        $namaEmail .= ", " . $namaList[$i];
        
    }
}
if($c == true)
{  
    if($logo == "https://pondok-huda.com") {
        $logo = "https://pondok-huda.com/Assets/images/logo/default-logo-black.png";
    }
    include '../pdf/TCPDF-master/tcpdf.php';
    $pdf = new TCPDF();
    // remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    
    // set margins
    $pdf->SetMargins(0, 0, 0, true);
    
    // set auto page breaks false
    $pdf->SetAutoPageBreak(false, 0);
    $pdf->AddPage('P', 'A4');
    $img_file = '../kwitansi/template-invoice.jpg';
    // Display image on full page
    $pdf->Image($img_file, 0, 0, 210, 297, 'JPG', '', '', true, 200, '', false, false, 0, false, false, true);
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(0);
    $pdf->SetFooterMargin(0);
    // set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    
    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    // $pdf->Image($img_file, 0, 0, 210, 297,
    
    if($totalbayarsebelumnya > 0)
    {
        $html = '
            <html>
                <head>
                </head>
                <body>
                    <table width="100%" cellpadding="0" cellspacing="0" border="1">
                        <tr>
                            <td height="180px" colspan="6"></td>
                        </tr>
                        <tr>
                            <td width="10%"></td>
                            <td colspan="2" width="40%">
                                    <img src="'.$logo.'" width="60px" height="60px"><br>
                                    '.$namakost.'
                            </td>
                            <td colspan="3" width="50%" align="right">No. '.$nokw.'</td>
                        </tr>
                        <tr>
                            <td colspan="6" height="85px"></td>
                        </tr>
                        <tr>
                            <td colspan="3" width="50%">
                                <table border="1" width="100%">
                                    <tr>
                                        <td width="5%"></td>
                                        <td width="50%">
                                            Detail Pembayaran       
                                        </td>
                                        <td width="45%" align="right">
                                            '.$metode.'
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" height="25px"></td>
                                    </tr>
                                    <tr>
                                        <td width="5%"></td>
                                        <td width="50%">
                                            Detail Bayar       
                                        </td>
                                        <td width="45%" align="right">
                                            Jumlah
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" height="25px"></td>
                                    </tr>
                                    <tr>
                                        <td width="5%"></td>
                                        <td width="50%">
                                            Sisa Pembayaran sebelum
                                        </td>
                                        <td width="45%" align="right">
                                            '.$sisabayarsebelumnya.'
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" height="25px"></td>
                                    </tr>
                                    <tr>
                                        <td width="5%"></td>
                                        <td width="50%">
                                            Total Bayar       
                                        </td>
                                        <td width="45%" align="right">
                                            '.$totalbayar.'
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" height="25px"></td>
                                    </tr>
                                    <tr>
                                        <td width="5%"></td>
                                        <td width="50%">
                                            Sisa Pembayaran       
                                        </td>
                                        <td width="45%" align="right">
                                            '.$sisasaatini.'
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td colspan="3" width="50%">
                            <table border="1" width="100%">
                                    <tr>
                                        <td width="10%"></td>
                                        <td width="90%">
                                            Untuk Pembayaran Sewa    
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" height="25px"></td>
                                    </tr>
                                    <tr>
                                        <td width="10%"></td>
                                        <td width="90%">
                                            Periode :
                                            <br>
                                            '.$periodkwitansi.'
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" height="25px"></td>
                                    </tr>
                                    <tr>
                                        <td width="10%"></td>
                                        <td width="90%">
                                            Status Bayar :
                                            <br>
                                            '.$status.'
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </body>
            </html>';
    }
    else
    {
        $html = '
            <html>
                <head>
                </head>
                <body>
                    <table width="100%" cellpadding="0" cellspacing="0" border="1">
                        <tr>
                            <td height="180px" colspan="6"></td>
                        </tr>
                        <tr>
                            <td width="10%"></td>
                            <td colspan="2" width="40%">
                                    <img src="'.$logo.'" width="60px" height="60px"><br>
                                    '.$namakost.'
                            </td>
                            <td colspan="3" width="50%" align="right">No. '.$nokw.'</td>
                        </tr>
                        <tr>
                            <td colspan="6" height="85px"></td>
                        </tr>
                        <tr>
                            <td colspan="3" width="50%">
                                <table border="1" width="100%">
                                    <tr>
                                        <td width="5%"></td>
                                        <td width="50%">
                                            Detail Pembayaran       
                                        </td>
                                        <td width="45%" align="right">
                                            '.$metode.'
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" height="25px"></td>
                                    </tr>
                                    <tr>
                                        <td width="5%"></td>
                                        <td width="50%">
                                            Detail Bayar       
                                        </td>
                                        <td width="45%" align="right">
                                            Jumlah
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" height="25px"></td>
                                    </tr>
                                    <tr>
                                        <td width="5%"></td>
                                        <td width="50%">
                                            Harga Kamar x Periode       
                                        </td>
                                        <td width="45%" align="right">
                                            '.$hargaperbulan.' x '.$period.'
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" height="25px"></td>
                                    </tr>
                                    <tr>
                                        <td width="5%"></td>
                                        <td width="50%">
                                            Denda       
                                        </td>
                                        <td width="45%" align="right">
                                            '.$denda.'
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" height="25px"></td>
                                    </tr>
                                    <tr>
                                        <td width="5%"></td>
                                        <td width="50%">
                                            Diskon       
                                        </td>
                                        <td width="45%" align="right">
                                            '.$diskon.'
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" height="90px"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" align="right">Total Harga '.$totalharga.'</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" height="25px"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" align="right">Dibayarkan '.$totalbayar.'</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" height="25px"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" align="right">Sisa Pembayaran '.$sisabayar.'</td>
                                    </tr>
                                </table>
                            </td>
                            <td colspan="3" width="50%">
                            <table border="1" width="100%">
                                    <tr>
                                        <td width="10%"></td>
                                        <td width="90%">
                                            Untuk Pembayaran Sewa    
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" height="25px"></td>
                                    </tr>
                                    <tr>
                                        <td width="10%"></td>
                                        <td width="90%">
                                            Periode :
                                            <br>
                                            '.$periodkwitansi.'
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" height="25px"></td>
                                    </tr>
                                    <tr>
                                        <td width="10%"></td>
                                        <td width="90%">
                                            Status Bayar :
                                            <br>
                                            '.$status.'
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </body>
            </html>';
    }
    
    $pdf->writeHTML($html, true, false, true, false, '');
    
    // $pdf->Output();
    
    // Save PDF to server
    $pdf->Output('/home/pondokhu/public_html/kwitansi/invoice.pdf', 'F');
    $c = false;
}
// ---------------------------SEND EMAIL--------------------------------
//recipient
for($i = 0; count($emailList) > $i; $i++)
{
    $to = $emailList[$i];

    //sender
    $from = 'huda@superemail.com';
    $fromName = 'PondokHuda';
    
    //email subject
    $subject = 'Pembayaran Sewa Kost'; 
    
    //attachment file path
    $file = "../kwitansi/invoice.pdf";
    
    //email body content
    $htmlContent = '<h1>Pembayaran Sewa Kost Berhasil</h1>
        <p>Jika tidak menerima kwitansi/invoice silahkan hubungi admin.</p>';
    
    //header for sender info
    $header = '';
    $headers = "From: $fromName"." <".$from.">";
    
    
    //boundary 
    $semi_rand = md5(time()); 
    $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
    
    //headers for attachment 
    $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 
    
    //multipart boundary 
    $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" .
    "Content-Transfer-Encoding: 7bit\n\n" . $htmlContent . "\n\n"; 
    
    //preparing attachment
    if(!empty($file) > 0){
        if(is_file($file)){
            $message .= "--{$mime_boundary}\n";
            $fp =    @fopen($file,"rb");
            $data =  @fread($fp,filesize($file));
    
            @fclose($fp);
            $data = chunk_split(base64_encode($data));
            $message .= "Content-Type: application/octet-stream; name=\"".basename($file)."\"\n" . 
            "Content-Description: ".basename($file)."\n" .
            "Content-Disposition: attachment;\n" . " filename=\"".basename($file)."\"; size=".filesize($file).";\n" . 
            "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
        }
    }
    $message .= "--{$mime_boundary}--";
    $returnpath = "-f" . $from;
    
    //send email
    $mail = @mail($to, $subject, $message, $headers, $returnpath); 
    
    //email sending status
    // echo $mail?"<h1>Mail sent. ".$cekemail."</h1>":"<h1>Mail sending failed.</h1>";
}

echo json_encode(array("kwitansi" => "kwitansi berhasil dikirim")); 

?>