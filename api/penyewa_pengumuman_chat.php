<?php

include ("kon.php");

$kBerita = $_POST['_kodeberita'];
$kUser = $_POST['_kodeuser'];
$keyUser = $_POST['_keyuser'];
$message = $_POST['_message'];
date_default_timezone_set("Asia/Bangkok");
$dttime = date("Y-m-d H:i:s");

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if( !$koneksi)
{
    return;
}

if( $kBerita != "" && 
    $kUser != "" && 
    $keyUser != "" && 
    $message != "")
{
    $hasil = array();
    
    $query = "INSERT INTO tb_beritakost_chat(kode_chat, kode_berita, kode_user, key_user, date_time, message) VALUES (NULL, '$kBerita', '$kUser', '$keyUser', '$dttime', '$message')";
    $result = mysqli_query($koneksi, $query);
    
    if( $result)
    {
        // echo json_encode(array('chat' => "chatinput"));
        $querySelect = "SELECT * FROM tb_beritakost_chat WHERE kode_berita = '$kBerita' AND kode_user = '$kUser' AND message = '$message' AND date_time = '$dttime'";
        $result2 = mysqli_query($koneksi, $querySelect);
        
        // echo "query2: " . $querySelect;
        
        if( mysqli_num_rows($result2) > 0)
        {
            while($rows2 = mysqli_fetch_assoc($result2))
            {
                $hasil[] = array(
                    "kodchat" => $rows2['kode_chat'],
                    "kodberita" => $rows2['kode_berita'],
                    "koduser" => $rows2['kode_user'],
                    "keyuser" => $rows2['key_user'],
                    "dttime" => $rows2['date_time'],
                    "msg" => $rows2['message']
                    );
            }
            
            echo json_encode(array('chat' => $hasil));
        }
        else
        {
            echo json_encode(array('chat' => "tidak ada chat"));
        }
    }
    else
        echo json_encode(array('chat' => "error input"));
}
else
{
    echo json_encode(array('chat' => "datakosong"));
}
?>