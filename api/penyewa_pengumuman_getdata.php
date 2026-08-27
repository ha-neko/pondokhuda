<?php

include ("kon.php");

$user_log = $_POST['_kode'];
// $user_log = "000001";
$akses = "";
$koneksi = mysqli_connect($host, $user, $pass, $daba);

if( !$koneksi)
{
    header('Content-Type: application/json');
    echo json_encode(array('pengumuman' => 'koneksi database gagal'));
    return;
}

// tentukan admin, owner, penyewa
if( (substr($user_log, 0, 1) == "p" || substr($user_log, 0, 1) == "u") &&
(substr($user_log, 1,1) == "a" || substr($user_log, 1, 1) == "b"))
{
    
    $akses = "penyewa";
}
else if((substr($user_log, 0, 1) == "o" && substr($user_log, 7, 1) == "w"))
{
    
    $akses = "owner";
}
else if((substr($user_log, 0, 1) == "a" && substr($user_log, 6, 1) == "d"))
{
    
    $akses = "admin";
}


if( $user_log != "")
{
    $query_chat = "SELECT * FROM tb_beritakost_chat WHERE kode_user = '$user_log' ORDER BY date_time ASC";
    // $query_chat = "SELECT * FROM tb_beritakost_chat ORDER BY date_time ASC";
    $result0 = mysqli_query($koneksi, $query_chat);
    
    $chatuser = array();
    $hasil = array();
    
    if( mysqli_num_rows($result0) > 0)
    {
        while($rows = mysqli_fetch_assoc($result0))
        {
            $chatuser[] = array(
                "kodchat" => $rows['kode_chat'],
                "kodberita" => $rows['kode_berita'],
                "koduser" => $rows['kode_user'],
                "keyuser" => $rows['key_user'],
                "dttime" => $rows['date_time'],
                "msg" => $rows['message']
                );
        }
        
        // $chatuser = json_encode(array("chat" => $chatuser));
        // $chatuser = json_encode(array($chatuser));
    }
    else
    {
        $chatuser[] = null;
    }
    
    // ------------------------------------------------------------
    // query untuk get berita yang tampil-------------------------------------------------------
if( $akses == "penyewa")
{
    $query = "SELECT kode,judul,berita,tglpublish,lastupdate FROM tb_beritakost WHERE status = 'tampil' AND kode_kost = (SELECT tb_penyewa.kode_kost FROM tb_penyewa WHERE tb_penyewa.kode = '$user_log') ORDER BY tglpublish DESC";
    
}
else if( $akses == "owner" || $akses == "admin")
{
    $query = "SELECT kode,judul,berita,tglpublish,lastupdate FROM tb_beritakost WHERE status = 'tampil'";
}
    
    $result = mysqli_query($koneksi, $query);
    
    if( mysqli_num_rows($result) > 0)
    {
        while($rows1 = mysqli_fetch_assoc($result))
        {   
            
            // echo "\nrows1: " . $rows1['kode'];
            $chatsetiappertanyaan = array();
            
            foreach($chatuser as $cu => $has)
            {
                $chatuserwithkode = array();
                // echo "\nbandingin antara rows1: " . $rows1['kode'] . ", dengan has: " . $has['kodberita'];
                $h;
                
                if( $has['kodberita'] == $rows1['kode'])
                {
                    $h = "..masuk ke array";
                    $chatuserwithkode = array(
                        "kodchat" => $has['kodchat'],
                        "kodberita" => $has['kodberita'],
                        "koduser" => $has['koduser'],
                        "keyuser" => $has['keyuser'],
                        "dttime" => $has['dttime'],
                        "msg" =>  $has['msg']
                        );
                        
                    array_push($chatsetiappertanyaan, $chatuserwithkode);    
                }
                else
                {
                    $h = "..tidak masuk array";
                }
                
                // echo "end of has: " . $has['dttime'] . " is " . $h . "\n";
                // push chatuserwithkode ke array A
            }
            
            // echo "end of rows1: " . $rows1['kode'] . "\n\n";
            
            // push array A ke $hasil, "chat" => $chatuserwithkode
            $hasil[] = array(
                "kode" => $rows1['kode'],
                "judul" => $rows1['judul'],
                "berita" => $rows1['berita'],
                "tglpublish" => $rows1['tglpublish'],
                "lastupdate" => $rows1['lastupdate'],
                "chat" => $chatsetiappertanyaan
                );
        }
        
        // $test = array();
        // foreach($hasil as $has => $nilai)
        // {
        //     foreach($nilai as $n)
        //     {
        //         echo "aa: " . $has . " ---> " . $nilai . " ----> " . $n . "\n";
        //     }
            
        // }
        // echo json_encode($test);
        
        // echo "ysm: " . $hasil[0]['judul'];
        
        $hasil = json_encode(array('pengumuman' => $hasil));
        //echo $hasil;
    }
    else
    {
        echo json_encode(array('pengumuman' => "tidak ada data pengumuman"));
    }
    
    echo $hasil;
}
else
{
    echo json_encode(array('pengumuman' => "error"));
}
?>