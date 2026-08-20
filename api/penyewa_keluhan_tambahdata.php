<?php

// ------------------tambah satu array assosiatif untuk get nama kategori

include ("kon.php");

$penyewa = $_POST['_penyewa'];
$judul = $_POST['_judul'];
$kategori = $_POST['_kategori'];
$uraian = $_POST['_uraian'];
$foto = $_POST['_foto'];

$tgl = date('Y-m-d H:i:s');

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if( !$koneksi)
{
    return;
}

$queryinsertkelistkeluhan = "INSERT INTO tb_keluhan_list(judul, kategori, uraian, tgl, user) VALUES ('$judul',(SELECT kode_keluhan FROM tb_keluhan_kategori WHERE kategori_keluhan = '$kategori'),'$uraian','$tgl','$penyewa')";

$resultqueryinsertlistkeluhan = mysqli_query($koneksi, $queryinsertkelistkeluhan);

if( $resultqueryinsertlistkeluhan)
{
    $querybalikan = "SELECT id FROM tb_keluhan_list WHERE judul = '$judul' AND uraian = '$uraian' AND tgl = '$tgl'";
    $resultquerybalikan = mysqli_query($koneksi, $querybalikan);
    
    if( mysqli_num_rows($resultquerybalikan) == 1)
    {
        $idinput;
        while($rows = mysqli_fetch_assoc($resultquerybalikan))
        {
            $idinput = $rows['id'];
        }
        
        $queryinsertkeupdatetgl = "INSERT INTO tb_keluhan_updatetgl(kode, status, tglupdate) VALUES ($idinput, 'Pelaporan', '$tgl')";
        $resultinsertupdatetgl = mysqli_query($koneksi, $queryinsertkeupdatetgl);
        if( $resultinsertupdatetgl)
        {
            // IMAGE PROPERTIES
            // $HOME_SERVER = "/home/pondokhu/public_html";
            // $USER_IMAGE_FOLDER = "/Assets/images/keluhan/";
            // $target_directory = $HOME_SERVER . $USER_IMAGE_FOLDER;
            // $filename = $_FILES['_foto']['name'];
            // $filesize = $_FILES['_foto']['size'];
            // $file_tmp = $_FILES['_foto']['tmp_name'];
            // $filetype = $_FILES['_foto']['type'];
            // $tmp = explode('.', $filename);
            // $file_ext = strtolower(end($tmp));
            // $target_file_to_move = $target_directory . $idinput . "." . $file_ext;
            // $uploadOK = 1;
            
            // $messageERROR;
            // // cek if file is existed
            // if( file_exists($target_file_to_move)){
            //   $uploadOK = 0;
            //   $messageERROR = "FILE EXIST";
            // } 
            // // cek if file limit
            // if( $filesize > 1500000){
            //   $uploadOK = 0;
            //   $messageERROR = "FILE SIZE";
            // } 
            // // cek limit file type
            // if( $filetype != "image/jpg" && $filetype != "image/png" && $filetype != "image/jpeg") {
            //     $uploadOK = 0;
            //     $messageERROR = "FILE TYPE";
            // }
            
            // // UPLOT
            // if( $uploadOK == 0){
            //     echo json_encode(array('daftarpenyewa' => "file foto tidak bisa diupload: ". $messageERROR));
            // }
            // else
            // {
            //     // write path for send to DB
            //     $alamatfoto = $USER_IMAGE_FOLDER . $idinput . "." . $file_ext;
                
            //     if(move_uploaded_file($file_tmp, $target_file_to_move))
            //     {
            //         $q = "INSERT INTO tb_keluhan_foto(id, urlfoto, time_created) VALUES ($idinput, '$alamatfoto', '$tgl')";
                    
            //         $r = mysqli_query($koneksi, $q);
                    
            //         if( $r)
            //         {
            //             echo json_encode(array('insertkeluhanbaru' => 'input keluhan berhasil'));
            //         }
            //         else
            //         {
            //             echo json_encode(array('insertkeluhanbaru' => "error input foto"));
            //         }
            //     }
            // }
            
            // $alamatfoto = "https://pondok-huda.com/Assets/images/keluhan/" . $idinput . "jpg";
            if( $foto != null)
            {
                $alamatfoto = "/Assets/images/keluhan/" . $idinput . ".jpg";
            }
            else
            {
                $alamatfoto = NULL;
            }
            
            if( $foto!= null)
            {
                $q = "INSERT INTO tb_keluhan_foto(id, urlfoto, time_created) VALUES ($idinput, '$alamatfoto', '$tgl')";
                $result_q = mysqli_query($koneksi, $q);
                
                if( $result_q)
                {
                    if( $foto != null)
                    {
                        file_put_contents("/home/pondokhu/public_html/Assets/images/keluhan/" . $idinput . ".jpg", base64_decode($foto));
                    }
                    
                    echo json_encode(array('insertkeluhanbaru' => "inputkeluhansukes"));
                }
                else
                {
                    echo json_encode(array('insertkeluhanbaru' => "error input foto"));
                }
            }
            else
            {
                echo json_encode(array('insertkeluhanbaru' => "inputkeluhantanpafotosukses"));
            }
        }
        else
        {
            echo json_encode(array('insertkeluhanbaru' => "error insert ke update tgl"));
        }
    }
    else
    {
        echo json_encode(array('insertkeluhanbaru' => "gagal ambil data keluhan di list"));
    }
}
else
{
    echo json_encode(array('insertkeluhanbaru' => "gagal insert data ke list"));
}
?>