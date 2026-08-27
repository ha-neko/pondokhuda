<?php

// ------------------tambah satu array assosiatif untuk get nama kategori

include ("kon.php");

$user_log = $_POST['_kode'];
$pin = $_POST['_pin'];
if(isset($_POST['_kodekost']))
{
    $kodekost = $_POST['_kodekost'];
}

$akses = "entah";

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if( !$koneksi)
{
    header('Content-Type: application/json');
    echo json_encode(array('getdatakeluhan' => 'koneksi database gagal'));
    return;
}

$query;
$numrows = 0;

if( (substr($user_log, 0, 1) == "p" || substr($user_log, 0, 1) == "u") &&
(substr($user_log, 1,1) == "a" || substr($user_log, 1, 1) == "b"))
{
    $query = "SELECT * FROM tb_penyewa WHERE kode = '$user_log' AND nomorpin = '$pin'";
    $numrows = mysqli_num_rows(mysqli_query($koneksi, $query));
    $akses = "penyewa";
}
else if((substr($user_log, 0, 1) == "o" && substr($user_log, 4, 1) == "w"))
{
    $query = "SELECT * FROM tb_owner WHERE kode = '$user_log' AND pin = '$pin'";
    $numrows = mysqli_num_rows(mysqli_query($koneksi, $query));
    $akses = "owner";
}
else if((substr($user_log, 0, 1) == "a" && substr($user_log, 4, 1) == "d"))
{
    $query = "SELECT * FROM tb_admin WHERE kode = '$user_log' AND pin = '$pin'";
    $numrows = mysqli_num_rows(mysqli_query($koneksi, $query));
    $akses = "admin";
}

$daftarkategorikeluhan = array();
if( $numrows == 1)
{
    $hasilall = array();
    
    // ambil kategori-kategori keluhan
    $querygetkategorikeluhan = "SELECT * FROM tb_keluhan_kategori";
    $resultkategorikeluhan = mysqli_query($koneksi, $querygetkategorikeluhan);
    
    if( mysqli_num_rows($resultkategorikeluhan) > 0)
    {
        while($rowkategori = mysqli_fetch_assoc($resultkategorikeluhan))
        {
            $daftarkategorikeluhan[] = array(
                'kodekategori' => $rowkategori['kode_keluhan'],
                'kategori' => $rowkategori['kategori_keluhan']
                );
        }
    }
    else
    {
        $daftarkategorikeluhan = null;
    }
    
    // echo json_encode(array('kategori' => $daftarkategorikeluhan));
    
    // END OF ----- ambil kategori-kategori keluhan--------------------------
    
    $query_chat = "";
    
    if( $akses == "penyewa")
    {
        // ada kondisi untuk menentukan query dimana penyewa, admin, owner   
        $query_chat = "SELECT * FROM tb_keluhan_chat WHERE kode_user = '$user_log' ORDER BY date_time ASC";
    }
    else if( $akses == "owner" || $akses == "admin")
    {
        $query_chat = "SELECT * FROM tb_keluhan_chat";
    }
    $result0 = mysqli_query($koneksi, $query_chat);
    
    $chatuser = array();    // isi chat2 dari user $user_log untuk setiap keluhan yang ada
    $updatekeluhanperstatus = array();
    $fotofotoperkeluhan = array();
    $komenperkeluhan = array();
    
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
    
    // ------------------------------------------------------------
    // query untuk get komen di masing-masing keluhan
    // ------------------------------------------------------------
    if( $akses == "penyewa")
    {
        $querygetkomen = "SELECT * FROM tb_keluhan_komenadmin 
            INNER JOIN tb_keluhan_list ON tb_keluhan_list.id = tb_keluhan_komenadmin.kode_kel
            WHERE tb_keluhan_list.user = '$user_log'
            ORDER BY created DESC";
    }
    else if( $akses == "owner" || $akses == "admin")
    {
        $querygetkomen = "SELECT * FROM tb_keluhan_komenadmin ORDER BY tb_keluhan_komenadmin.created DESC";
    }
    $resultkomen = mysqli_query($koneksi, $querygetkomen);
    if( mysqli_num_rows($resultkomen) > 0)
    {
        while($rows = mysqli_fetch_assoc($resultkomen))
        {
            $komenperkeluhan[] = array(
                "idkeluhan" => $rows['kode_kel'],
                "komen" => $rows['komen'],
                "created" => $rows['created']
                );
        }
    }
    else
    {
        $komenperkeluhan[] = null;
    }
    
    
    
    // ------------------------------------------------------------
    
    // --------------------------------------------------------------
    
    // ------------------------------------------------------------
    // query untuk get keluhan yang tampil, isikan dengan array TglUpdateKeluhan, FOTO-FOTO, dan CHAT
    // ada kondisi untuk menentukan query dimana penyewa, admin, owner
    
    if( $akses == "penyewa")
    {
        $query = "SELECT 
                    tb_keluhan_list.id , 
                    tb_keluhan_list.judul, 
                    tb_keluhan_kategori.kategori_keluhan AS kategori,
                    tb_keluhan_list.uraian, 
                    tb_keluhan_list.tgl, 
                    tb_keluhan_list.user,
                    tb_penyewa.nama,
                    tb_penyewa.nohp AS HP
                    
                    FROM tb_keluhan_list 
                            
                    INNER JOIN tb_keluhan_kategori ON tb_keluhan_kategori.kode_keluhan = tb_keluhan_list.kategori 
                    INNER JOIN tb_penyewa ON tb_penyewa.kode = tb_keluhan_list.user
                    
                    WHERE user = '$user_log' ORDER BY tb_keluhan_list.tgl DESC";
    }
    else if( $akses == "owner" || $akses == "admin")
    {
        // $query = "SELECT tb_keluhan_list.id, tb_keluhan_list.judul, tb_keluhan_kategori.kategori_keluhan AS kategori, tb_keluhan_list.uraian, tb_keluhan_list.tgl, tb_penyewa.nokamar AS nokamar, tb_penyewa.nama AS nama, tb_penyewa.nohp AS HP, tb_keluhan_list.user FROM tb_keluhan_list INNER JOIN tb_keluhan_kategori ON tb_keluhan_kategori.kode_keluhan = tb_keluhan_list.kategori INNER JOIN tb_penyewa ON tb_penyewa.kode = tb_keluhan_list.user";
        $query = "SELECT 
                    	tb_keluhan_list.id,
                        tb_keluhan_kategori.kategori_keluhan AS kategori,
                        tb_keluhan_list.judul,
                        tb_keluhan_list.uraian,
                        tb_keluhan_list.tgl,
                        tb_keluhan_list.user,
                        tb_kamar.nokamar AS nokamar,
                        tb_penyewa.nama,
                        tb_penyewa.nohp AS HP,
                        (SELECT tb_keluhan_updatetgl.status FROM tb_keluhan_updatetgl WHERE tb_keluhan_updatetgl.kode = tb_keluhan_list.id ORDER BY tb_keluhan_updatetgl.status DESC LIMIT 0,1) AS status
                        
                    FROM
                    	tb_keluhan_list
                    
                    INNER JOIN tb_penyewa ON tb_penyewa.kode = tb_keluhan_list.user
                    INNER JOIN tb_keluhan_kategori ON tb_keluhan_kategori.kode_keluhan = tb_keluhan_list.kategori
                    INNER JOIN tb_penyewa_kamar ON tb_penyewa_kamar.kode_penyewa = tb_keluhan_list.user
                    INNER JOIN (SELECT * FROM tb_sewa_kamar WHERE tanggal_selesai IS NULL) sewa_kamar ON sewa_kamar.kode_sewa = tb_penyewa_kamar.kode_sewa
                    INNER JOIN tb_kamar ON tb_kamar.id = sewa_kamar.kode_kamar
                    INNER JOIN tb_kost ON tb_kost.kode_kost = tb_penyewa.kode_kost
                    WHERE tb_penyewa.kode_kost='$kodekost'
                    
                    ORDER BY tb_keluhan_list.tgl DESC";
        
        // $query = "SELECT 
        //             DISTINCT tb_keluhan_list.id, 
        //             tb_keluhan_list.judul, 
        //             tb_keluhan_kategori.kategori_keluhan AS kategori, 
        //             tb_keluhan_list.uraian, 
        //             tb_keluhan_list.tgl, 
        //             tb_penyewa.nama AS nama, 
        //             tb_penyewa.nohp AS HP, 
        //             tb_keluhan_list.user,
        //             (SELECT tb_keluhan_updatetgl.status FROM tb_keluhan_updatetgl WHERE tb_keluhan_updatetgl.kode = tb_keluhan_list.id ORDER BY tb_keluhan_updatetgl.status DESC LIMIT 0,1) AS status
                    
        //             FROM tb_keluhan_list 
                    
        //             INNER JOIN tb_keluhan_kategori ON tb_keluhan_kategori.kode_keluhan = tb_keluhan_list.kategori 
        //             INNER JOIN tb_penyewa ON tb_penyewa.kode = tb_keluhan_list.user
        //             INNER JOIN tb_penyewa_kamar ON tb_penyewa.kode = tb_penyewa_kamar.kode_penyewa
        //             INNER JOIN tb_sewa_kamar ON tb_penyewa_kamar.kode_sewa = tb_sewa_kamar.kode_sewa
        //             INNER JOIN tb_keluhan_updatetgl ON tb_keluhan_updatetgl.kode = tb_keluhan_list.id
                    
        //             ORDER BY tb_keluhan_list.tgl DESC";
    }
    
    $result = mysqli_query($koneksi, $query);
    
    if( mysqli_num_rows($result) > 0)
    {
        while($rows1 = mysqli_fetch_assoc($result))
        {   
            $chatsetiapkeluhan = array();
            $fotosetiapkeluhan = array();
            $statussetiapkeluhan = array();
            $komensetiapkeluhan = array();
            
            // get komen
            foreach($komenperkeluhan as $kom => $haskom)
            {
                $komenwithkode = array();
                if( $haskom['idkeluhan'] == $rows1['id'])
                {
                    $komenwithkode = array(
                        "kode" => $haskom['idkeluhan'],
                        "komen" => $haskom['komen'],
                        "created" => $haskom['created']
                        );
                        
                    array_push($komensetiapkeluhan, $komenwithkode);
                }
            }
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
            if( $akses == "penyewa")
            {
                $hasil[] = array(
                    "kode" => $rows1['id'],
                    "judul" => $rows1['judul'],
                    "kategori" => $rows1['kategori'],
                    "uraian" => $rows1['uraian'],
                    "tgl" => $rows1['tgl'],
                    "user" => $rows1['user'],
                    // "nokamar" => $rows1['nokamar'],
                    "nama" => $rows1['nama'],
                    "hp" => $rows1['HP'],
                    "chat" => $chatsetiapkeluhan,
                    "foto" => $fotosetiapkeluhan,
                    "status" => $statussetiapkeluhan,
                    "komen" =>$komensetiapkeluhan
                    );
            }
            else
            {
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
                    "status" => $statussetiapkeluhan,
                    "komen" => $komensetiapkeluhan
                    );
            }
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
        
        // $hasil = json_encode(array('keluhan' => $hasil));
        // array_push($hasilall, $hasil);
        //echo $hasil;
    }
    else
    {
        // $hasil[] = null;
    }
    
    echo json_encode(array('kategorikeluhan' => $daftarkategorikeluhan, 'keluhan' => $hasil));
}
else
{
    echo json_encode(array('getdatakeluhan' => "tidakadadatamembervalid"));
}
?>