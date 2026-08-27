<?php

include ("kon.php");

ph_rate_limit('lupapassword', 10, 900, 300);

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if (!$koneksi)
{
    header('Content-Type: application/json');
    echo json_encode(array('hasil' => 'koneksi database gagal'));
    return;
}

$_email = $_POST['emailcheck'];

$query = "SELECT * FROM tb_penyewa";
$result = mysqli_query($koneksi, $query);

$_isada = false;
$kodedanpin = array();
$kode;
$pin;

while($rows = mysqli_fetch_assoc($result))
{
    if( $rows['email'] == $_email)
    {
        $_isada = true;
        $kodedanpin[] = array(
            "kode" => $rows['kode']
            );
        
        $kode = $rows['kode'];
        $pin = $rows['nomorpin'];
        break;
    }
}

if( $_isada)
{
    // pin is only sent by email, never exposed in the http response
    $sent = ph_send_mail($_email, "Lupa Password", "<b>Kode dan PIN anda</b><br>Kode: ". $kode . "<br>PIN: ". $pin . "<br><br><<< ---NO-EMAIL-REPLY--- >>>", ph_html_mail_headers());
    if ($sent) {
        echo json_encode(array('hasil'=>$kodedanpin));
    } else {
        http_response_code(502);
        echo json_encode(array('hasil'=>'gagal_kirim_email'));
    }
}
else
    echo json_encode(array('hasil'=>"tidakada"));
?>
