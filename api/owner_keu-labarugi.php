<?php

include ("kon.php");

$bulan = $_POST['_bulan'];
$tahun = $_POST['_tahun'];
$kodekost = $_POST['_kodekost'];

$koneksi = mysqli_connect($host, $user, $pass, $daba);

if( !$koneksi)
{
  return;
}

$querygetpendapatan = "SELECT tb_keu_akun.kode_akun, tb_keu_akun.nama_akun,
                    tb_keu_jurnal_umum.saldo
                    FROM tb_keu_akun
                    LEFT JOIN (
                        SELECT tb_keu_akun.kode_akun, tb_keu_akun.nama_akun, SUM(tb_keu_jurnal_umum.jumlah) AS saldo
                        FROM tb_keu_akun
                        JOIN tb_keu_jurnal_umum USING (kode_akun)
                        WHERE MONTH(tanggal) = '$bulan'
                        AND YEAR(tanggal) = '$tahun'
                        AND kode_kost = '$kodekost'
                        AND posisi = CASE WHEN kode_akun!='1-0100'
                                               THEN 'Kredit'
                                               ELSE 'Debit' END
                        AND (kode_jenis_akun=5 OR kode_akun='1-0100')
                        GROUP BY tb_keu_akun.kode_akun
                        ORDER BY tb_keu_akun.kode_akun
                    ) tb_keu_jurnal_umum ON tb_keu_akun.kode_akun=tb_keu_jurnal_umum.kode_akun
                    WHERE kode_jenis_akun=5 OR tb_keu_akun.kode_akun='1-0100'
                    GROUP BY tb_keu_akun.kode_akun
                    ORDER BY tb_keu_akun.kode_akun";

$resultgetpendapatan = mysqli_query($koneksi, $querygetpendapatan);
$total = 0;
$totalneto = 0; // pendapatan kost - diskon
$totallainlain = 0; // pendapatan kost - diskon
if(mysqli_num_rows($resultgetpendapatan) > 0)
{
	while($rows = mysqli_fetch_assoc($resultgetpendapatan))
    {
        if($rows['kode_akun'] == "5-1100")
        {
            if($rows['saldo'] != null)
            {
                $pendapatankost = $rows['saldo'];
            }
            else
            {
                $pendapatankost = 0;
            }
            $totalneto += $pendapatankost;
        }
        
        if($rows['kode_akun'] == "1-0100")
        {
            if($rows['saldo'] != null)
            {
                $diskon = $rows['saldo'];
            }
            else
            {
                $diskon = 0;
            }
            $totalneto -= $diskon;
        }
        
        if($rows['kode_akun'] == "5-2100")
        {
            if($rows['saldo'] != null)
            {
                $pendapatandenda = $rows['saldo'];
            }
            else
            {
                $pendapatandenda = 0;
            }
            $totallainlain += $pendapatandenda;
        }
        
        if($rows['kode_akun'] == "5-2200")
        {
            if($rows['saldo'] != null)
            {
                $pendapatanpindah = $rows['saldo'];
            }
            else
            {
                $pendapatanpindah = 0;
            }
            $totallainlain += $pendapatanpindah;
        }
        
        if($rows['kode_akun'] == "5-2300")
        {
            if($rows['saldo'] != null)
            {
                $pendapatanlainnya = $rows['saldo'];
            }
            else
            {
                $pendapatanlainnya = 0;
            }
            $totallainlain += $pendapatanlainnya;
        }
        
        if($rows['kode_akun'] != '1-0100')
        {
            $total += $rows['saldo'];
        }
        else
        {
            $total -= $rows['saldo'];
        }
    }
    
    $pendapatan[] = array(
        "pendapatankost"    => $pendapatankost,
        "diskon"            => $diskon,
        "totalneto"         => $totalneto,
        "pendapatandenda"   => $pendapatandenda,
        "pendapatanpindah"   => $pendapatanpindah,
        "pendapatanlainnya" => $pendapatanlainnya,
        "totallainlain"     => $totallainlain,
        "total"             => $total
    );
}
else
{
	$pendapatan = "error get pendapatan ".mysqli_error($koneksi);
}

$querygetbeban = "SELECT tb_keu_akun.kode_akun, tb_keu_akun.nama_akun, tb_keu_jurnal_umum.saldo
                    FROM tb_keu_akun
                    LEFT JOIN (
                        SELECT tb_keu_akun.kode_akun, tb_keu_akun.nama_akun, SUM(tb_keu_jurnal_umum.jumlah) AS saldo
                        FROM tb_keu_akun
                        JOIN tb_keu_jurnal_umum USING (kode_akun)
                        WHERE MONTH(tanggal) = '$bulan'
                        AND YEAR(tanggal) = '$tahun'
                        AND kode_kost = '$kodekost'
                        AND posisi = 'Debit'
                        AND kode_jenis_akun=4
                        GROUP BY tb_keu_akun.kode_akun
                        ORDER BY tb_keu_akun.kode_akun
                    ) tb_keu_jurnal_umum ON tb_keu_akun.kode_akun=tb_keu_jurnal_umum.kode_akun
                    WHERE kode_jenis_akun=4
                    GROUP BY tb_keu_akun.kode_akun
                    ORDER BY tb_keu_akun.kode_akun";

$resultgetbeban = mysqli_query($koneksi, $querygetbeban);
$total = 0;
if(mysqli_num_rows($resultgetbeban) > 0)
{
	while($rows = mysqli_fetch_assoc($resultgetbeban))
    {
        if($rows['kode_akun'] == "4-1100")
        {
            if($rows['saldo'] != null)
            {
                $gajitunjangan = $rows['saldo'];
            }
            else
            {
                $gajitunjangan = 0;
            }
        }
        
        if($rows['kode_akun'] == "4-1200")
        {
            if($rows['saldo'] != null)
            {
                $perbaikan = $rows['saldo'];
            }
            else
            {
                $perbaikan = 0;
            }
        }
        
        if($rows['kode_akun'] == "4-1300")
        {
            if($rows['saldo'] != null)
            {
                $tukang = $rows['saldo'];
            }
            else
            {
                $tukang = 0;
            }
        }
        
        if($rows['kode_akun'] == "4-1400")
        {
            if($rows['saldo'] != null)
            {
                $listrik = $rows['saldo'];
            }
            else
            {
                $listrik = 0;
            }
        }
        
        if($rows['kode_akun'] == "4-1500")
        {
            if($rows['saldo'] != null)
            {
                $air = $rows['saldo'];
            }
            else
            {
                $air = 0;
            }
        }
        
        if($rows['kode_akun'] == "4-1600")
        {
            if($rows['saldo'] != null)
            {
                $sampah = $rows['saldo'];
            }
            else
            {
                $sampah = 0;
            }
        }
        
        if($rows['kode_akun'] == "4-1700")
        {
            if($rows['saldo'] != null)
            {
                $iuran = $rows['saldo'];
            }
            else
            {
                $iuran = 0;
            }
        }
        
        if($rows['kode_akun'] == "4-1800")
        {
            if($rows['saldo'] != null)
            {
                $internet = $rows['saldo'];
            }
            else
            {
                $internet = 0;
            }
        }
        
        if($rows['kode_akun'] == "4-1900")
        {
            if($rows['saldo'] != null)
            {
                $pajak = $rows['saldo'];
            }
            else
            {
                $pajak = 0;
            }
        }
        
        if($rows['kode_akun'] == "4-2100")
        {
            if($rows['saldo'] != null)
            {
                $lainlain = $rows['saldo'];
            }
            else
            {
                $lainlain = 0;
            }
        }
        
        $total += $rows['saldo'];
    }
    
    $beban[] = array(
        "gajitunjangan" => $gajitunjangan,
        "perbaikan"     => $perbaikan,
        "tukang"        => $tukang,
        "listrik"       => $listrik,
        "air"           => $air,
        "sampah"        => $sampah,
        "iuran"         => $iuran,
        "internet"      => $internet,
        "pajak"         => $pajak,
        "lainlain"      => $lainlain,
        "total"         => $total
    );
}
else
{
	$beban = "error get pendapatan ".mysqli_error($koneksi);
}

echo json_encode(array(
    'pendapatan'    => $pendapatan,
    'beban'         => $beban));

?>