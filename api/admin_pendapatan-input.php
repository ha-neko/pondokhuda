<?php

include ("kon.php");
include ("admin_log.php");
include ("owner_log.php");

$kode_sewa = $_POST['_kode_sewa'];
$kodekamar = $_POST['_kodekamar'];
$nokamar = $_POST['_nokamar'];
$tanggal_pembayaran = $_POST['_tanggal_pembayaran'];
$periode_bayar = $_POST['_periode_bayar'];
$harga_perbulan = $_POST['_harga_perbulan'];
$denda = $_POST['denda'];
$denda_prev = $_POST['_denda_sebelumnya'];
$diskon = $_POST['diskon'];
$diskon_prev = $_POST['_diskon_sebelumnya'];
$harga_pindah_prev = $_POST['_harga_pindah_sebelumnya'];
$total_bayar_prev = $_POST['_total_bayar_sebelumnya'];
$sisa_bayar = $_POST['_sisa_bayar'];
$total_harga = $_POST['_total_harga'];
$total_harga_prev = $_POST['_total_harga_sebelumnya'];
$total_bayar = $_POST['_total_bayar'];
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

$querygetkdbayar = "SELECT kode_bayar
                    FROM tb_bayar_kost
                    JOIN tb_sewa_kamar USING (kode_sewa)
                    INNER JOIN tb_kamar 
                    ON tb_sewa_kamar.kode_kamar = tb_kamar.id
                    WHERE MONTH(tanggal_bayar)=MONTH(NOW())
                    AND YEAR(tanggal_bayar)=YEAR(NOW())
                    AND tb_kamar.kode_kost='$kodekost'
                    ORDER BY SUBSTRING(kode_bayar, -6) DESC LIMIT 0,1";
           
$resultgetkdbayar = mysqli_query($koneksi, $querygetkdbayar);

if( mysqli_num_rows($resultgetkdbayar) > 0)
{
    while($rows = mysqli_fetch_assoc($resultgetkdbayar))
    {
        $kode_bayar = $rows['kode_bayar'];
    }
    
    $kode_pembayaran = GenerateKodePembayaran($kode_bayar, $nokamar, $kodekost);
}
else
{
    $kode_pembayaran = 'KW-'.date('Y/m/').$nokamar.'/'.$kodekost.'/000001';
}

$tanggal_bayar = date("Y-m-d");
$isInsert = false;
// INSERT DATA PEMBAYARAN -----------------------------------------
$queryinsertpembayaran = "INSERT INTO tb_bayar_kost(kode_bayar, kode_sewa,
                          tanggal_pembayaran, tanggal_bayar, periode_bayar,
                          harga_perbulan, denda, denda_sebelumnya, diskon,
                          diskon_sebelumnya, harga_pindah_sebelumnya,
                          total_bayar_sebelumnya, sisa_bayar_sebelumnya,
                          total_harga, total_bayar,
                          metode)
                          SELECT '$kode_pembayaran', '$kode_sewa',
                          '$tanggal_pembayaran', '$tanggal_bayar',
                          '$periode_bayar', '$harga_perbulan',
                          '$denda', '$denda_prev', '$diskon', '$diskon_prev',
                          IF($sisa_bayar=0, 0, '$harga_pindah_prev'),
                          IF($sisa_bayar=0, 0, '$total_bayar_prev'),
                          '$sisa_bayar',
                          IF($sisa_bayar=0, '$total_harga', '$total_harga_prev'),
                          '$total_bayar', '$metode'";

$resultinsertpembayaran = mysqli_query($koneksi, $queryinsertpembayaran);

if($resultinsertpembayaran)
{
  $isInsert = true;
}
else
{
  $datapembayaran = 'data pembayaran gagal diinput, Error: '. mysqli_error($koneksi);
}

if ($isInsert)
{
  $queryinsertlaporankeu = "INSERT INTO tb_keu_jurnal_umum
                            (kode_transaksi,
                            kode_akun, tanggal, keterangan,
                            posisi, jumlah, kode_kost)
                            VALUES ";

  if ($metode == 'Tunai')
  {
    $queryinsertlaporankeu .= "('$kode_pembayaran',
                              '1-1100', '$tanggal_bayar', 'Kas',
                              'Debit', $total_bayar, $kodekost)";
  }
  else
  {
    $queryinsertlaporankeu .= "('$kode_pembayaran',
                              '1-1200', '$tanggal_bayar', 'Bank',
                              'Debit', $total_bayar, $kodekost)";
  }

  if ($diskon != 0)
  {
    $queryinsertlaporankeu .= ",('$kode_pembayaran',
                              '1-0100', '$tanggal_bayar', 'Diskon',
                              'Debit', $diskon, $kodekost)";
  }

  $queryinsertlaporankeu .= ",('$kode_pembayaran',
                              '5-1100', '$tanggal_bayar',
                              CONCAT('Pendapatan Sewa Kamar ', '$nokamar'),
                              'Kredit', ($total_bayar + $diskon) - $denda, $kodekost)";

  if ($denda != 0)
  {
    $queryinsertlaporankeu .= ",('$kode_pembayaran',
                              '5-2100', '$tanggal_bayar', 
                              CONCAT('Denda Sewa Kamar ', '$nokamar'),
                              'Kredit', $denda, $kodekost)";
  }

  $resultinsertlaporankeu = mysqli_query($koneksi, $queryinsertlaporankeu);
    
  if($resultinsertlaporankeu)
  {
    $datapembayaran = 'data pembayaran berhasil diinput'; // 3.1
  }
  else
  {
    $datapembayaran = 'data laporan keuangan gagal diinput, Error: '. mysqli_error($koneksi);
  }
  
  // Email Attachment
  $cekemail = "";
  $nama = "";
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
    
    CONCAT('Pembayaran awal di bulan ', 
    IF(MONTH(tb_bayar_kost.periode_bayar)=1,'Januari',
    IF(MONTH(tb_bayar_kost.periode_bayar)=2,'Februari',
    IF(MONTH(tb_bayar_kost.periode_bayar)=3,'Maret',
    IF(MONTH(tb_bayar_kost.periode_bayar)=4,'April',
    IF(MONTH(tb_bayar_kost.periode_bayar)=5,'Mei',
    IF(MONTH(tb_bayar_kost.periode_bayar)=6,'Juni',
    IF(MONTH(tb_bayar_kost.periode_bayar)=7,'Juli',
    IF(MONTH(tb_bayar_kost.periode_bayar)=8,'Agustus',
    IF(MONTH(tb_bayar_kost.periode_bayar)=9,'September',
    IF(MONTH(tb_bayar_kost.periode_bayar)=10,'Oktober',
    IF(MONTH(tb_bayar_kost.periode_bayar)=11,'November',
    IF(MONTH(tb_bayar_kost.periode_bayar)=12,'Desember','')))))))))))),
    ' s.d ',
    MONTH(DATE_ADD((SELECT tb_bayar_kost.tanggal_pembayaran FROM tb_bayar_kost WHERE tb_bayar_kost.kode_sewa = (SELECT tb_sewa_kamar.kode_sewa FROM tb_sewa_kamar WHERE tb_sewa_kamar.kode_kamar = '$kodekamar' AND tb_sewa_kamar.tanggal_selesai IS NULL LIMIT 0,1) ORDER BY tb_bayar_kost.kode_bayar DESC LIMIT 0,1), INTERVAL $periode_bayar MONTH))),
    
    CONCAT(DATE_FORMAT((SELECT tb_bayar_kost.tanggal_pembayaran FROM tb_bayar_kost WHERE tb_bayar_kost.kode_sewa = (SELECT tb_sewa_kamar.kode_sewa FROM tb_sewa_kamar WHERE tb_sewa_kamar.kode_kamar = '$kodekamar' AND tb_sewa_kamar.tanggal_selesai IS NULL LIMIT 0,1) ORDER BY tb_bayar_kost.kode_bayar DESC LIMIT 0,1),'%d/%m/%Y')
           
    ,' s.d. ',
    DATE_FORMAT(DATE_ADD((SELECT tb_bayar_kost.tanggal_pembayaran FROM tb_bayar_kost WHERE tb_bayar_kost.kode_sewa = (SELECT tb_sewa_kamar.kode_sewa FROM tb_sewa_kamar WHERE tb_sewa_kamar.kode_kamar = '$kodekamar' AND tb_sewa_kamar.tanggal_selesai IS NULL LIMIT 0,1) ORDER BY tb_bayar_kost.kode_bayar DESC LIMIT 0,1), INTERVAL $periode_bayar MONTH),'%d/%m/%Y')
    )    
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
      echo "Query Error " . mysqli_error($koneksi);
  }
  
    for($i = 0; count($namaList) > $i; $i++)
    {
        if($nama == "")
        {
            $nama = $namaList[$i];
        }
        else
        {
            $nama .= ", " . $namaList[$i];
            
        }
    }
    $invoiceFile = null;
    if($c == true)
    {  
        if($logo == "https://pondok-huda.com") {
            $logo = "https://pondok-huda.com/Assets/images/logo/default-logo-black.png";
        }
        require_once ph_receipt_asset('tcpdf');
        // $pdf = new TCPDF();
        // create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // set margins
        $pdf->SetMargins(0, 0, 0, true);
        
        // set auto page breaks false
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->AddPage('P', 'A4');
        $img_file = ph_receipt_asset('template');
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
        $invoiceFile = ph_invoice_temp_file();
        $pdf->Output($invoiceFile, 'F');
        $c = false;
    }
    // ---------------------------SEND EMAIL--------------------------------
    //recipient
    for($i = 0; count($emailList) > $i; $i++)
    {
        $to = $emailList[$i];
    
        //sender
        $from = ph_mail_from();
        $fromName = ph_mail_from_name();
        
        //email subject
        $subject = 'Pembayaran Sewa Kost'; 
        
        //attachment file path
        $file = $invoiceFile;
        
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
        $mail = is_string($file) && is_file($file) && ph_send_mail($to, $subject, $message, $headers);
        
        //email sending status
        // echo $mail?"<h1>Mail sent. ".$cekemail."</h1>":"<h1>Mail sending failed.</h1>";
    }
    if (isset($invoiceFile) && is_file($invoiceFile)) {
        @unlink($invoiceFile);
    }
    // ------------------------END SEND EMAIL-----------------------------
}

/*if($isInsert)
{
  // 1.1 bayar telat
  if ($tanggal_bayar > $tanggal_pembayaran)
  {
    // 2.1 bayar sebulan
    if ($periode_bayar == '1')
    {
      $queryinsertlaporankeu = "INSERT INTO tb_keu_jurnal_umum
                            (kode_transaksi, kode_akun, tanggal, keterangan,
                            posisi, jumlah, kode_kost)
                            
                            VALUES ";

      // 3.1 bayar tunai
      if ($metode == 'Tunai')
      {
          $queryinsertlaporankeu .="(CONCAT('$kode_pembayaran', '-2'),
                      '1-1100', '$tanggal_bayar', 'Kas',
                      'Debit', $total_bayar, '$kodekost')";
      }
      else
      {
          $queryinsertlaporankeu .="(CONCAT('$kode_pembayaran', '-2'),
                      '1-1200', '$tanggal_bayar', 'Bank',
                      'Debit', $total_bayar, '$kodekost')";
      }
            
      if ($diskon != '0')
      {
          $queryinsertlaporankeu .= ",(CONCAT('$kode_pembayaran', '-2'),
                                  '1-0100', '$tanggal_bayar', 'Diskon',
                                  'Debit', $diskon, '$kodekost')";
      }
      
      $queryinsertlaporankeu .= ",(CONCAT('$kode_pembayaran', '-2'),
                                  '1-1300', '$tanggal_bayar',
                                  CONCAT('Piutang Sewa Kamar ', $nokamar),
                                  'Kredit', ($total_bayar + $diskon) - $denda,
                                  '$kodekost')";
      
      if ($denda != '0')
      {
          $queryinsertlaporankeu .= ",(CONCAT('$kode_pembayaran', '-2'),
                                  '5-2100', '$tanggal_bayar',
                                  CONCAT('Denda Sewa Kamar ', $nokamar),
                                  'Kredit', $denda, '$kodekost')";
      }

      $resultinsertlaporankeu = mysqli_query($koneksi, $queryinsertlaporankeu);
      
      if($resultinsertlaporankeu)
      {
          $datapembayaran = 'data pembayaran berhasil diinput'; // 3.1
      }
      else
      {
          $datapembayaran = 'data laporan keuangan gagal diinput, Error: '. mysqli_error($koneksi);
      }
    }
    // 2.2 lebih dari sebulan
    else
    {
      //get nominal piutang
      $querygetpiutang = "SELECT harga FROM tb_piutang_kost
                  WHERE kode_sewa='$kode_sewa'
                  AND tanggal_piutang='$tanggal_pembayaran'";
                  
      $resultgetpiutang = mysqli_query($koneksi, $querygetpiutang);

      if (mysqli_num_rows($resultgetpiutang) > 0)
      {
        while($rows = mysqli_fetch_assoc($resultgetpiutang))
        {
            $piutang = $rows['harga'];
        }

        $queryinsertlaporankeu = "INSERT INTO tb_keu_jurnal_umum
                              (kode_transaksi, kode_akun, tanggal,
                              keterangan, posisi, jumlah, kode_kost)

                              VALUES";

        if ($metode == 'Tunai')
        {
          $queryinsertlaporankeu = "(CONCAT('$kode_pembayaran', '-2'),
                                   '1-1100', '$tanggal_bayar', 'Kas',
                                   'Debit', $piutang + $denda, '$kodekost')";
        }
        else
        {
            $queryinsertlaporankeu .="(CONCAT('$kode_pembayaran', '-2'),
                                     '1-1200', '$tanggal_bayar', 'Bank',
                                     'Debit', $piutang + $denda, '$kodekost')";
        }

        $queryinsertlaporankeu .= ",(CONCAT('$kode_pembayaran', '-2'),
                                  '1-1300', '$tanggal_bayar',
                                  CONCAT('Piutang Sewa Kamar ', $nokamar),
                                  'Kredit', $piutang, '$kodekost')";
        
        if ($denda != '0')
        {
            $queryinsertlaporankeu .= ",(CONCAT('$kode_pembayaran', '-2'),
                                    '5-2100', '$tanggal_bayar',
                                    CONCAT('Denda Sewa Kamar ', $nokamar),
                                    'Kredit', $denda, '$kodekost')";
        }

        $resultinsertlaporankeu = mysqli_query($koneksi, $queryinsertlaporankeu);

        if($resultinsertlaporankeu)
        {
          $queryinsertlaporankeu = "INSERT INTO tb_keu_jurnal_umum
                                    (kode_transaksi, kode_akun, tanggal,
                                    keterangan, posisi, jumlah, kode_kost)
                                    VALUES";

          if ($metode == 'Tunai')
          {
            $queryinsertlaporankeu = "(CONCAT('$kode_pembayaran', '-3'), '1-1100',
                                      '$tanggal_bayar', 'Kas', 'Debit',
                                      $total_bayar - ($piutang + $denda), $kodekost)";
          }
          else
          {
              $queryinsertlaporankeu .="(CONCAT('$kode_pembayaran', '-3'), '1-1200',
                                      '$tanggal_bayar', 'Bank', 'Debit',
                                      $total_bayar - ($piutang + $denda), $kodekost)";
          }

          if ($diskon != '0')
          {
              $queryinsertlaporankeu .= ",(CONCAT('$kode_pembayaran', '-3'),
                                      '1-0100', '$tanggal_bayar', 'Diskon',
                                      'Debit', $diskon, '$kodekost')";
          }

          $queryinsertlaporankeu .= "(CONCAT('$kode_pembayaran', '-3'), '1-1400', '$tanggal_bayar',
                                    CONCAT('Sewa Diterima Di Muka Kamar ', $nokamar),
                                    'Kredit', ($total_bayar - ($piutang + $denda)) + $diskon, $kodekost)";

          $resultinsertlaporankeu = mysqli_query($koneksi, $queryinsertlaporankeu);

          if($resultinsertlaporankeu)
          {
            $datapembayaran = 'data pembayaran berhasil diinput'; // 3.1 b
          }
          else
          {
            $datapembayaran = 'data laporan keuangan gagal diinput, Error: '. mysqli_error($koneksi);
          }
        }
        else
        {
          $datapembayaran = 'data laporan keuangan gagal diinput, Error: '. mysqli_error($koneksi);
        }
      }
      else
      {
        $datapembayaran = 'Error get data piutang: '. mysqli_error($koneksi);
      }
    }
  }
  // 1.2 bayar tepat waktu
  else
  {
    // 2.1 bayar sebulan
    if ($periode_bayar == '1')
    {
      $queryinsertlaporankeu = "INSERT INTO tb_keu_jurnal_umum
                            (kode_transaksi,
                            kode_akun, tanggal, keterangan,
                            posisi, jumlah, kode_kost)
                            VALUES ";

      if ($metode == 'Tunai')
      {
        $queryinsertlaporankeu .= "(CONCAT('$kode_pembayaran', '-1'),
                                  '1-1100', '$tanggal_bayar', 'Kas',
                                  'Debit', $total_bayar, $kodekost)";
      }
      else
      {
        $queryinsertlaporankeu .= "(CONCAT('$kode_pembayaran', '-1'),
                                  '1-1200', '$tanggal_bayar', 'Bank',
                                  'Debit', $total_bayar, $kodekost)";
      }

      if ($diskon != 0)
      {
        $queryinsertlaporankeu .= ", (CONCAT('$kode_pembayaran', '-1'),
                                  '1-0100', '$tanggal_bayar', 'Diskon',
                                  'Debit', $diskon, $kodekost)";
      }

      $queryinsertlaporankeu .= ",(CONCAT('$kode_pembayaran', '-1'),
                                  '5-1100', '$tanggal_bayar',
                                  CONCAT('Pendapatan Sewa Kamar ', $nokamar),
                                  'Kredit', ($total_bayar + $diskon) - $denda, $kodekost)";

      if ($denda != 0)
      {
        $queryinsertlaporankeu .= ", (CONCAT('$kode_pembayaran', '-1'),
                                  '5-2100', '$tanggal_bayar', 
                                  CONCAT('Denda Sewa Kamar ', $nokamar),
                                  'Kredit', $denda, $kodekost)";
      }

      $resultinsertlaporankeu = mysqli_query($koneksi, $queryinsertlaporankeu);
        
      if($resultinsertlaporankeu)
      {
        $datapembayaran = 'data pembayaran berhasil diinput'; // 3.1
      }
      else
      {
        $datapembayaran = 'data laporan keuangan gagal diinput, Error: '. mysqli_error($koneksi);
      }
    }
    // 2.2 bayar lebih dari sebulan
    else
    {
      $queryinsertlaporankeu = "INSERT INTO tb_keu_jurnal_umum
                            (kode_transaksi,
                            kode_akun, tanggal, keterangan,
                            posisi, jumlah, kode_kost)
                            VALUES ";

      if ($metode == 'Tunai')
      {
        $queryinsertlaporankeu .= "(CONCAT('$kode_pembayaran', '-1'),
                                  '1-1100', '$tanggal_bayar', 'Kas',
                                  'Debit', $total_bayar, $kodekost)";
      }
      else
      {
        $queryinsertlaporankeu .= "(CONCAT('$kode_pembayaran', '-1'),
                                  '1-1200', '$tanggal_bayar', 'Bank',
                                  'Debit', $total_bayar, $kodekost)";
      }

      if ($diskon != 0)
      {
        $queryinsertlaporankeu .= ", (CONCAT('$kode_pembayaran', '-1'),
                                  '1-0100', '$tanggal_bayar', 'Diskon',
                                  'Debit', $diskon, $kodekost)";
      }

      $queryinsertlaporankeu .= ",(CONCAT('$kode_pembayaran', '-3'),
                                  '1-1400', '$tanggal_bayar',
                                  CONCAT('Sewa Diterima Di Muka Kamar ', $nokamar),
                                  'Kredit', ($total_bayar + $diskon) - $denda, $kodekost)";

      if ($denda != 0)
      {
        $queryinsertlaporankeu .= ", (CONCAT('$kode_pembayaran', '-1'),
                                  '5-2100', '$tanggal_bayar', 
                                  CONCAT('Denda Sewa Kamar ', $nokamar),
                                  'Kredit', $denda, $kodekost)";
      }

      $resultinsertlaporankeu = mysqli_query($koneksi, $queryinsertlaporankeu);
        
      if($resultinsertlaporankeu)
      {
        $datapembayaran = 'data pembayaran berhasil diinput'; // 3.1
      }
      else
      {
        $datapembayaran = 'data laporan keuangan gagal diinput, Error: '. mysqli_error($koneksi);
      }
    }
  }
}*/

if(isset($kodeadmin))
{
    $log = CreateLogAdmin($koneksi, $namaadmin.' menginput data pembayaran nomor kamar '.$nokamar.' atas nama penyewa '.$nama.'. sebesar '.$totalbayar, $kodeadmin, $kodekost);
}

if(isset($kodeowner))
{
    $log = CreateLogOwner($koneksi, $namaowner.' menginput data pembayaran nomor kamar '.$nokamar.' atas nama penyewa '.$nama.'. sebesar '.$totalbayar, $kodeowner, $kodekost);
}

echo json_encode(array('datapembayaran' => $datapembayaran));

function GenerateKodePembayaran($kode, $nokamar, $kodekost)
{
    // kode = KW-thn/bln/nokamar/nourut (no urut = 6 digit)
    $noUrutTerakhir = substr($kode, -6);
    
    $noUrutBaru =  $noUrutTerakhir + 1;
    
    $hasil = 'KW-'.date('Y/m/').$nokamar.'/'.$kodekost.'/'.substr($noUrutTerakhir, 0, -1 * abs(strlen($noUrutBaru))).$noUrutBaru;
    
    return $hasil;
}

?>
