<?php

include ("kon.php");

$koneksi = mysqli_connect($host, $user, $pass, $daba);

$kodekost = $_POST['_kodekost'];

if (!$koneksi)
{
    header('Content-Type: application/json');
    echo json_encode(array('pengumuman' => 'koneksi database gagal'));
    return;
}

// array untuk ngambil chat
$querychat = "SELECT tb_beritakost_chat.kode_chat, tb_beritakost_chat.kode_berita,
    tb_beritakost_chat.kode_user, tb_beritakost_chat.key_user,
    tb_beritakost_chat.date_time, tb_beritakost_chat.message,
    tb_kamar.nokamar, tb_penyewa.nama
    FROM tb_beritakost_chat
    INNER JOIN tb_penyewa ON tb_penyewa.kode = tb_beritakost_chat.kode_user
    INNER JOIN tb_penyewa_kamar ON tb_penyewa.kode = tb_penyewa_kamar.kode_penyewa
    INNER JOIN tb_sewa_kamar ON tb_penyewa_kamar.kode_sewa = tb_sewa_kamar.kode_sewa
    INNER JOIN tb_kamar ON tb_sewa_kamar.kode_kamar = tb_kamar.id
    ORDER BY date_time ASC";
    
$resultchat = mysqli_query($koneksi, $querychat);

$arraychat = array();
$finalpengumumanadmin = array();

if( mysqli_num_rows($resultchat) > 0)
{
    while($rowschat = mysqli_fetch_assoc($resultchat))
    {
        $arraychat[] = array(
            "kodchat" => $rowschat['kode_chat'],
            "kodberita" => $rowschat['kode_berita'],
            "koduser" => $rowschat['kode_user'],
            "keyuser" => $rowschat['key_user'],
            "dttime" => $rowschat['date_time'],
            "msg" => $rowschat['message'],
            "nokamar" => $rowschat['nokamar'],
            "namapenyewa" => $rowschat['nama']
            );
    }
    
    // echo json_encode($arraychat);
}
else
{
    $arraychat = null;
}

// array ngambil berita
$query = "SELECT * FROM tb_beritakost WHERE kode_kost='$kodekost'";
$result = mysqli_query($koneksi, $query);

$hasil = array();

if( mysqli_num_rows($result) > 0)
{
    while($rows1 = mysqli_fetch_assoc($result))
    {
        // echo "\nrows1: " . $rows1['kode'];
            $chatsetiappertanyaan = array();
            
            /*foreach($arraychat as $cu => $has)
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
                        "msg" =>  $has['msg'],
                        "nokamar" => $has['nokamar'],
                        "namapenyewa" => $has['namapenyewa']
                        );
                        
                    array_push($chatsetiappertanyaan, $chatuserwithkode);    
                }
                else
                {
                    $h = "..tidak masuk array";
                }
                
                // echo "end of has: " . $has['dttime'] . " is " . $h . "\n";
                // push chatuserwithkode ke array A
            }*/
            
            // echo "end of rows1: " . $rows1['kode'] . "\n\n";
            
            // push array A ke $hasil, "chat" => $chatuserwithkode
            $hasil[] = array(
                "kode" => $rows1['kode'],
                "judul" => $rows1['judul'],
                "berita" => $rows1['berita'],
                "tglpublish" => $rows1['tglpublish'],
                "lastupdate" => $rows1['lastupdate'],
                "status" =>  $rows1['status'],
                //"chat" => $chatsetiappertanyaan
                );
                
        // $hasil[] = $rows;
        $finalpengumumanadmin = json_encode(array('pengumuman' => $hasil));
    }
    
    echo $finalpengumumanadmin;
}
else
{
    echo json_encode(array('pengumuman' => "error"));
}

?>