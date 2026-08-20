<?php

// ------------------tambah satu array assosiatif untuk get nama kategori

include ("kon.php");

// $user_log = $_POST['_kode'];
$user_log = "uuu";
// $akses = $_POST['_akses'];
// ------------------tambah satu key untuk menetukan apa dia penyewa, admin, owner

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if( !$koneksi)
{
    return;
}

if( $user_log != "")
{
 // ada kondisi untuk menentukan query dimana penyewa, admin, owner   
    // $query_chat = "SELECT * FROM tb_keluhan_chat WHERE kode_user = '$user_log' ORDER BY date_time ASC";
    $query_chat = "SELECT * FROM tb_keluhan_chat ORDER BY date_time ASC";
    $result0 = mysqli_query($koneksi, $query_chat);
    
    $chatuser = array();    // isi chat2 dari user $user_log untuk setiap keluhan yang ada
    $updatekeluhanperstatus = array();
    $fotofotoperkeluhan = array();
    
    $hasil = array();
    
    if( mysqli_num_rows($result0) > 0)
    {
        // echo "num: " . mysqli_num_rows($result0);
        while($rows = mysqli_fetch_assoc($result0))
        {
            $chatuser[] = array(
                "kodchat" => $rows['idchatkeluhan'],
                "kodkeluhan" => $rows['kode_keluhan'],
                "koduser" => $rows['kode_user'],
                "keyuser" => $rows['key_user'],
                "dttime" => $rows['date_time'],
                "msg" => $rows['message']
                );
        }
        
        // $chatuser = json_encode(array("chat" => $chatuser));
        // $chatuser = json_encode(array($chatuser));
        // echo "size: " . sizeof($chatuser);
    }
    else
    {
        $chatuser[] = null;
    }
    
    // ------------------------------------------------------------
    // query untuk get foto-foto -------------------------------------------------------
    $queryGetFoto = "SELECT * FROM tb_keluhan_foto";
    $resultGetFoto = mysqli_query($koneksi, $queryGetFoto);
    
    if( mysqli_num_rows($resultGetFoto) > 0)
    {
        while($rows3 = mysqli_fetch_assoc($resultGetFoto))
        {
            $fotofotoperkeluhan[] = array(
                "kode" => $rows3['id'],
                "url" => $rows3['urlfoto']
                );
        }
    }
    else
    {
        $fotofotoperkeluhan[] = null;
    }
    // --------------------------------------------------------------
    
    // ------------------------------------------------------------
    // query untuk get status keluhan-------------------------------------------------------
    $queryGetStatus = "SELECT * FROM tb_keluhan_updatetgl";
    $resultGetStatus = mysqli_query($koneksi, $queryGetStatus);
    if( mysqli_num_rows($resultGetStatus) > 0)
    {
        while($rows3 = mysqli_fetch_assoc($resultGetStatus))
        {
            $updatekeluhanperstatus[] = array(
                "kode" => $rows3['kode'],
                "status" => $rows3['status'],
                "tgl" => $rows3['tglupdate']
                );
        }
    }
    else
    {
        $updatekeluhanperstatus[] = null;
    }
    // --------------------------------------------------------------
    
    // ------------------------------------------------------------
    // query untuk get keluhan yang tampil, isikan dengan array TglUpdateKeluhan, FOTO-FOTO, dan CHAT
    // ada kondisi untuk menentukan query dimana penyewa, admin, owner
    
    // $query = "SELECT tb_keluhan_list.id, tb_keluhan_list.judul, tb_keluhan_kategori.kategori_keluhan AS kategori, tb_keluhan_list.uraian, tb_keluhan_list.tgl, tb_keluhan_list.user FROM tb_keluhan_list INNER JOIN tb_keluhan_kategori ON tb_keluhan_kategori.kode_keluhan = tb_keluhan_list.kategori WHERE user = '$user_log'";
    $query = "SELECT tb_keluhan_list.id, tb_keluhan_list.judul, tb_keluhan_kategori.kategori_keluhan AS kategori, tb_keluhan_list.uraian, tb_keluhan_list.tgl, tb_penyewa.nokamar AS nokamar, tb_penyewa.nama AS nama, tb_penyewa.nohp AS HP, tb_keluhan_list.user FROM tb_keluhan_list INNER JOIN tb_keluhan_kategori ON tb_keluhan_kategori.kode_keluhan = tb_keluhan_list.kategori INNER JOIN tb_penyewa ON tb_penyewa.kode = tb_keluhan_list.user";
    
    $result = mysqli_query($koneksi, $query);
    
    if( mysqli_num_rows($result) > 0)
    {
        while($rows1 = mysqli_fetch_assoc($result))
        {   
            $chatsetiapkeluhan = array();
            $fotosetiapkeluhan = array();
            $statussetiapkeluhan = array();
            
            // get data FOTO
            foreach($fotofotoperkeluhan as $fot => $has2)
            {
                $fotowithkode = array();
                if($has2['kode'] == $rows1['id'])
                {
                    $fotowithkode = array(
                        "id" => $has2['kode'],
                        "link" => $has2['url']
                        );
                        
                    array_push($fotosetiapkeluhan, $fotowithkode);
                }
            }
            
            // get data STATUS
            foreach($updatekeluhanperstatus as $upd => $has3)
            {
                $tglwithkode = array();
                if($has3['kode'] == $rows1['id'])
                {
                    $tglwithkode = array(
                        "id" => $has3['kode'],
                        "status" => $has3['status'],
                        "tgl" => $has3['tgl']
                        );
                        
                    array_push($statussetiapkeluhan, $tglwithkode);
                }
            }
            
            // get data CHAT
            foreach($chatuser as $cu => $has)
            {
                $chatuserwithkode = array();
                // echo "\nbandingin antara rows1: " . $rows1['kode'] . ", dengan has: " . $has['kodberita'];
                $h;
                // echo "kodekeluhan: " . $has['kodkeluhan'] . ", key_user: " . $has['keyuser'] . " id: " . $rows1['id'] . "\n";
                
                if( $has['kodkeluhan'] == $rows1['id'])
                {
                    $h = "..masuk ke array";
                    $chatuserwithkode = array(
                        "kodchat" => $has['kodchat'],
                        "kodkeluhan" => $has['kodkeluhan'],
                        "koduser" => $has['koduser'],
                        "keyuser" => $has['keyuser'],
                        "dttime" => $has['dttime'],
                        "msg" =>  $has['msg']
                        );
                        
                    array_push($chatsetiapkeluhan, $chatuserwithkode);    
                }
                else
                {
                    $h = "..tidak masuk array";
                }
            }
            
            // push array A ke $hasil, "chat" => $chatuserwithkode
            // Masuk
            $hasil[] = array(
                "kode" => $rows1['id'],
                "judul" => $rows1['judul'],
                "kategori" => $rows1['kategori'],
                "uraian" => $rows1['uraian'],
                "tgl" => $rows1['tgl'],
                "user" => $rows1['user'],
                "nokamar" => $rows1['nokamar'],
                "nama" => $rows1['nama'],
                "hp" => $rows1['HP'],
                "chat" => $chatsetiapkeluhan,
                "foto" => $fotosetiapkeluhan,
                "status" => $statussetiapkeluhan
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
        
        $hasil = json_encode(array('keluhan' => $hasil));
        //echo $hasil;
    }
    else
    {
        $hasil = json_encode(array('keluhan' => "tidak ada data keluhan"));
    }
    
    echo $hasil;
}
else
{
    echo json_encode(array('keluhan' => "error"));
}
?>