<?php

function CreateLogOwner($conn, $aktifitas, $kodeowner, $kodekost = NULL)
{
    $isInsert = false;
    if(isset($kodekost))
    {
        $queryinsertlog = "INSERT INTO tb_log_owner(aktifitas, kode_owner, kode_kost)
                           VALUES('$aktifitas', '$kodeowner', $kodekost)";
    }
    else
    {
        $queryinsertlog = "INSERT INTO tb_log_owner(aktifitas, kode_owner, kode_kost)
                           VALUES('$aktifitas', '$kodeowner', NULL)";
    }
    
    $resultinsertlog = mysqli_query($conn, $queryinsertlog);
    
    if($resultinsertlog)
    {
        $isInsert = true;    
    }
    
    return $isInsert;
}

?>