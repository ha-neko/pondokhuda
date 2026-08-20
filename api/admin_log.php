<?php

function CreateLogAdmin($conn, $aktifitas, $kodeadmin, $kodekost = NULL)
{
    $isInsert = false;
    if(isset($kodekost))
    {
        $queryinsertlog = "INSERT INTO tb_log_admin(aktifitas, kode_admin, kode_kost)
                           VALUES('$aktifitas', '$kodeadmin', $kodekost)";
    }
    else
    {
        $queryinsertlog = "INSERT INTO tb_log_admin(aktifitas, kode_admin, kode_kost)
                           VALUES('$aktifitas', '$kodeadmin', NULL)";
    }
    
    $resultinsertlog = mysqli_query($conn, $queryinsertlog);
    if($resultinsertlog)
    {
        $isInsert = true;
        
    }
    
    return $isInsert;
}

?>