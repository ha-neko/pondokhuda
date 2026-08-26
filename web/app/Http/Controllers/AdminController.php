<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AdminController extends Controller
{
    protected $index;

    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware(function ($request, $next) {

            if (count(Auth::user()->adminkost) > 0) {
                $this->index = Session::get('index');
                View::share('indexKost', $this->index);
                View::share('appName', Auth::user()->adminkost[$this->index]->kost->nama_kost);
            } else {
                View::share('appName');
            }

            return $next($request);
        });
    }

    public function kostGanti($index)
    {
        $this->index = $index;

        Session::put('index', $this->index);

        return redirect()->back();
    }

    public function dashboard()
    {

        $url = api_url('get_report.php');
        $data = array(
            '_kodekost' => Auth::user()->adminkost[$this->index]->kode_kost
        );
        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $result = api_post($url, $data);

        $json = json_decode($result, true);
        $data = [
            'pageTitle' => 'Admin Dashboard',
            'activeId' => 'dashboard',
            'activeIdSubMenu' => '',
        ];
        // return $json;
        return view('admin.dashboard', ['report' => $json], compact('data'));
    }

    /* --------------------------------------------------------------------------------------- */
    /* ------------------------------------- KELUHAN -------------------------------------- */
    /* --------------------------------------------------------------------------------------- */

    public function keluhan()
    {
        $url = api_url('keluhan_getdata.php');
        $kode = Auth::user()->kode;
        $pin = Auth::user()->pin;
        $data = array(
            '_kode' => $kode,
            '_pin' => $pin,
            '_kodekost' => Auth::user()->adminkost[$this->index]->kode_kost
        );
        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $result = api_post($url, $data);
        $data = [
            'pageTitle' => 'Admin Keluhan',
            'activeId' => 'keluhan',
            'activeIdSubMenu' => '',
        ];
        $json = json_decode($result, true);
        return view('admin.keluhan', ['keluhan' => $json], compact('data'));
    }

    public function getKomenKeluhan()
    {
        $url = api_url('keluhan_getdata.php');
        $kode = Auth::user()->kode;
        $pin = Auth::user()->pin;
        $data = array(
            '_kode' => $kode,
            '_pin' => $pin,
            '_kodekost' => Auth::user()->adminkost[$this->index]->kode_kost
        );
        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $result = api_post($url, $data);
        $data = [
            'pageTitle' => 'Admin Keluhan',
            'activeId' => 'keluhan',
            'activeIdSubMenu' => '',
        ];
        $json = json_decode($result, true);
        return $json;
    }

    public function tambahKomenKeluhan(Request $request)
    {
        $url = api_url('admin_keluhan-komen-tambah.php');
        $kodekel = $request->nilai1;
        $komen = $request->nilai2;
        $data = array(
            '_kodekeluhan'  => $kodekel,
            '_komen'        => $komen,
            '_kodeadmin'    => Auth::user()->kode,
            '_namaadmin'    => Auth::user()->nama,
            '_kodekost'     => Auth::user()->adminkost[$this->index]->kode_kost,
        );
        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $result = api_post($url, $data);
        $json = json_decode($result, true);
        // dd($json);
        return $json;
    }

    public function detailKeluhan($id)
    {
        $url = api_url('keluhan_getdata.php');
        $kode = Auth::user()->kode;
        $pin = Auth::user()->pin;
        $kodes = $id;
        $data = array(
            '_kode' => $kode,
            '_pin' => $pin,
            '_kodekost' => Auth::user()->adminkost[$this->index]->kode_kost
        );
        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $result = api_post($url, $data);
        $json = json_decode($result, true);
        $data = [
            'pageTitle' => 'Admin Detail Keluhan',
            'activeId' => 'keluhan',
            'activeIdSubMenu' => '',

        ];
        //dd($json);
        if ($json['keluhan'] != null) {
            return view('admin.detail-keluhan', ['keluhan' => $json], compact('data', 'kodes'));
        } else {
            return redirect()->route('admin.keluhan');
        }
    }

    public function updateKeluhan(Request $request, $id)
    {
        // dd($request->all());
        if ($request->has('dikerjakan')) {
            $url = api_url('admin_keluhan_ubahstatus.php');
            $kode = $id;
            $data = array(
                '_kode' => $kode,
                '_status' => 'Dikerjakan',
                '_kodeadmin'    => Auth::user()->kode,
                '_namaadmin'    => Auth::user()->nama,
                '_kodekost'     => Auth::user()->adminkost[$this->index]->kode_kost,
            );
            $option = array(
                'http' => array(
                    'header'  => api_header(),
                    'method' => 'POST',
                    'content' => http_build_query($data)
                )
            );
            $result = api_post($url, $data);

            // return $result;
            if ($result == '{"ubahstatuskeluhan":"status keluhan sukses diperbarui"}') {
                Session::flash('message', 'Status keluhan sukses diperbarui');
                return back();
            } else {
                Session::flash('message', 'Tidak ada perubahan status');
                return back();
            }
        } elseif ($request->has('selesai')) {
            $url = api_url('admin_keluhan_ubahstatus.php');
            $kode = $id;
            $data = array(
                '_kode' => $kode,
                '_status' => 'Selesai',
                '_kodeadmin'    => Auth::user()->kode,
                '_namaadmin'    => Auth::user()->nama,
                '_kodekost'     => Auth::user()->adminkost[$this->index]->kode_kost,
            );
            $option = array(
                'http' => array(
                    'header'  => api_header(),
                    'method' => 'POST',
                    'content' => http_build_query($data)
                )
            );
            $result = api_post($url, $data);

            // return $result;
            if ($result == '{"ubahstatuskeluhan":"status keluhan sukses diperbarui"}') {
                Session::flash('message', 'Status keluhan sukses diperbarui');
                return back();
            } else {
                Session::flash('message', 'Tidak ada perubahan status');
                return back();
            }
        } else {
            Session::flash('message', 'Tidak ada perubahan status');
            return back();
        }
    }

    public function updateOff()
    {
        Session::flash('message', 'Status keluhan tidak dapat diupdate karena keluhan sudah selesai');
        return back();
    }

    /* --------------------------------------------------------------------------------------- */
    /* ------------------------------------- PENYEWA -------------------------------------- */
    /* --------------------------------------------------------------------------------------- */

    public function penyewa()
    {
        $url = api_url('admin_penyewa-get-list.php');
        $data = array(
            '_kodekost' => Auth::user()->adminkost[$this->index]->kode_kost
        );
        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $result = api_post($url, $data);
        $json = json_decode($result, true);
        // dd($json);
        $data = [
            'pageTitle' => 'Admin Penyewa',
            'activeId' => 'penyewa',
            'activeIdSubMenu' => '',
        ];
        // dd($json);
        return view('admin.penyewa', ['penyewa' => $json], compact('data'));
    }

    public function penyewaResendEmailDaftar(Request $request)
    {
        $url = api_url('email_penyewa-pendaftaran.php');
        $data = array(
            '_kode' => $request->kode,
        );
        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $result = api_post($url, $data);

        return $result;
    }

    public function penyewaResendKwitansi(Request $request)
    {
        $url = api_url('email_penyewa-kwitansi.php');
        $data = array(
            '_kode' => $request->kode
        );
        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $result = api_post($url, $data);

        return $result;
    }

    public function penyewaStopSewa()
    {
        $url = api_url('admin_kamar-get-terisi.php');

        $data = array(
            '_kodekost' => Auth::user()->adminkost[$this->index]->kode_kost
        );

        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $result = api_post($url, $data);

        $json = json_decode($result, true);

        $data = [
            'pageTitle' => 'Admin Penyewa Berhenti Sewa',
            'activeId' => 'penyewa',
            'activeIdSubMenu' => '',
        ];
        return view('admin.penyewa-stop-sewa', ['nokamar' => $json], compact('data'));
    }

    public function stopSewa(Request $request)
    {
        $url = api_url('admin_penyewa-selesai.php');

        $data = array(
            '_kodesewa' => $request->kodesewa,
            '_kodepenyewa' => $request->kodepenyewa,
            '_namapenyewa' => $request->namapenyewa,
            '_kodekamar' => $request->nokamar,
            '_nokamar' => $request->nomerkamar,
            '_hargakamar' => $request->hargakosperbulan,
            '_tglpembayaran' => $request->tanggalpembayaran,
            '_periode' => $request->periodebayars,

            '_kodeadmin' => Auth::user()->kode,
            '_namaadmin' => Auth::user()->nama,
            '_kodekost' => Auth::user()->adminkost[$this->index]->kode_kost
        );

        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );

        $result = api_post($url, $data);

        // return $result;
        if ($result = '{"selesaisewa":"penyewa selesai menyewa"}') {
            Session::flash('message', 'Penyewa Berhasil Selesai Sewa', 'alert-success');
            return redirect(route('admin.penyewa'));
        } else {
            Session::flash('message', 'Gagal Stop Sewa', 'alert-danger');
            return redirect(route('admin.penyewa'));
        }
    }

    public function viewCreatePenyewa()
    {
        $url = api_url('admin_penyewa-create-getkamar.php');
        $url2 = api_url('wil_get-provinsi.php');
        $data = array(
            '_kodekost'     => Auth::user()->adminkost[$this->index]->kode_kost,
            '_jmlpenyewa'   => '1'
        );
        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $result = api_post($url, $data);
        $result2 = api_get($url2);
        $json = json_decode($result, true);
        $json2 = json_decode($result2, true);
        // dd($json);
        $data = [
            'pageTitle' => 'Admin Penyewa',
            'activeId' => 'penyewa',
            'activeIdSubMenu' => '',
        ];
        // dd($result2);
        return view('admin.create-penyewa', ['kamar' => $json, 'provinsi' => $json2], compact('data'));
    }

    public function getDataKamarCreate(Request $request)
    {
        $jumlahpenyewa = $request->jmlpenyewa;
        $url = api_url('admin_penyewa-create-getkamar.php');
        $data = array(
            '_kodekost'     => Auth::user()->adminkost[$this->index]->kode_kost,
            '_jmlpenyewa'   => $jumlahpenyewa
        );
        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $result = api_post($url, $data);

        return $result;
    }

    public function penyewaPindahKamar()
    {
        $url = api_url('admin_kamar-get-terisi.php');

        $data = array(
            '_kodekost' => Auth::user()->adminkost[$this->index]->kode_kost
        );

        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $result = api_post($url, $data);

        $json = json_decode($result, true);

        $data = [
            'pageTitle' => 'Admin Penyewa Pindah Kamar',
            'activeId' => 'penyewa',
            'activeIdSubMenu' => '',
        ];
        return view('admin.pindah-kamar', ['nokamar' => $json], compact('data'));
    }

    public function getDataSewaKamar(Request $request)
    {
        $url = api_url('admin_penyewa-pindah-get-datasewa.php');
        $kodekamar = $request->nilai;
        $data = array(
            '_kodekamar' => $kodekamar,
            '_kodekost' => Auth::user()->adminkost[$this->index]->kode_kost
        );
        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $result = api_post($url, $data);
        return $result;
    }

    public function hargakamar(Request $request)
    {
        $url = api_url('admin_kamar-get-harga.php');
        $kodekamar = $request->nilai;
        $data = array(
            '_kodekamar' => $kodekamar
        );
        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $result = api_post($url, $data);
        return $result;
    }

    public function getkotakab(Request $request)
    {
        $kotakab = $request->nilai;
        $url = api_url('wil_get-kotakab.php');
        $data = array(
            '_provinsi' => $kotakab
        );

        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $result = api_post($url, $data);

        return $result;
    }

    public function getkecamatan(Request $request)
    {
        $_provinsi = $request->nilai1;
        $_kotakab = $request->nilai2;
        $url = api_url('wil_get-kecamatan.php');
        $data = array(
            '_provinsi' => $_provinsi,
            '_kotakab' => $_kotakab
        );
        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $result = api_post($url, $data);

        return $result;
    }

    public function getkelurahan(Request $request)
    {
        $_provinsi = $request->nilai1;
        $_kotakab = $request->nilai2;
        $_kecamatan = $request->nilai3;
        $url = api_url('wil_get-kelurahan.php');
        $data = array(
            '_provinsi' => $_provinsi,
            '_kotakab' => $_kotakab,
            '_kecamatan' => $_kecamatan
        );
        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $result = api_post($url, $data);

        return $result;
    }

    public function getkodepos(Request $request)
    {
        $_provinsi = $request->nilai1;
        $_kotakab = $request->nilai2;
        $_kecamatan = $request->nilai3;
        $_kelurahan = $request->nilai4;
        $url = api_url('wil_get-kodepos.php');
        $data = array(
            '_provinsi' => $_provinsi,
            '_kotakab' => $_kotakab,
            '_kecamatan' => $_kecamatan,
            '_kelurahan' => $_kelurahan
        );
        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $result = api_post($url, $data);

        return $result;
    }

    public function pindahKamar(Request $request)
    {
        $kodesewa = $request->kodesewa;
        $kodepenyewa = $request->kodepenyewa;
        $namapenyewa = $request->namapenyewa;
        $kodekamarbaru = $request->kodekamarbaru;
        $nokamarbaru = $request->nokamarbaru;
        $kamarterisi = $request->kamarterisi;

        $kodekamarlama = $request->nokamar;
        $nokamarlama = $request->nomerkamar;
        $tglpembayaranlama = $request->tanggalpembayaran;
        $hargakamarlama = $request->hargakosperbulan;
        $periodebayarlama = $request->periodebayars;

        $tanggalpembayaranbaru = $request->tglsanggupbayar;
        $hargakamarbaru = $request->hargakamarbaru;
        $periodebayar = $request->periodebayarbaru;
        $diskon = $request->diskonbaru;
        $hargapindah = $request->bayarperhari;
        $totalharga = $request->totalhargabaru;
        $totalbayar = $request->totalbayarbaru;

        $kodekost = Auth::user()->adminkost[$this->index]->kode_kost;

        $url = api_url('admin_penyewa-pindah.php');
        $data = array(
            '_kodesewa' => $kodesewa,
            '_kodepenyewa' => $kodepenyewa,
            '_namapenyewa' => $namapenyewa,
            '_kodekamarbaru' => $kodekamarbaru,
            '_nokamarbaru' => $nokamarbaru,
            '_kamarterisi' => $kamarterisi,

            '_kodekamarlama' => $kodekamarlama,
            '_nokamarlama' => $nokamarlama,
            '_tglpembayaranlama' => $tglpembayaranlama,
            '_hargakamarlama' => $hargakamarlama,
            '_periodebayarlama' => $periodebayarlama,

            '_tglpembayaranbaru' => $tanggalpembayaranbaru,
            '_hargakamarbaru' => $hargakamarbaru,
            '_periodebayarbaru' => $periodebayar,
            '_diskon' => $diskon,
            '_hargapindah' => $hargapindah,
            '_totalharga' => $totalharga,
            '_totalbayar' => $totalbayar,

            '_kodeadmin'    => Auth::user()->kode,
            '_namaadmin'    => Auth::user()->nama,

            '_kodekost' => $kodekost
        );

        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $result = api_post($url, $data);
        // return $result;
        if ($result == '{"datapindah":"penyewa berhasil pindah"}') {
            Session::flash('message', 'Berhasil memindahkan penyewa');
            return redirect(route('admin.penyewa'));
        } else {
            Session::flash('message', 'Gagal memindahkan penyewa "ERROR : ' . $result . '"');
            return redirect(route('admin.pindahkamar'));
        }
    }

    public function createPenyewa(Request $request)
    {
        // $kode = $request->kode;
        // //dd($request->all());
        // if ($request->periodebayar <= 0 || $request->periodebayar == null) {
        //     Session::flash('message', 'Error input pada periode bayar');
        //     return redirect(route('admin.penyewa'));
        // }
        // else {
        //     if ($request->hasFile('image')) {
        //         $image = $request->file('image');
        //         $path = '/home/pondokhu/public_html/Assets/images/user/';
        //         $filename = $kode . "." . $image->getClientOriginalExtension();
        //         $image->move($path, $filename);
        //         return $kode;
        //     }
        //     else {
        //         Session::flash('message', 'Error input pada foto');
        //         return redirect(route('admin.penyewa'));
        //     }
        // }
        $kodekost = Auth::user()->adminkost[$this->index]->kode_kost;
        $kode = $request->kode;
        $noktp = $request->noktp;
        $nama = $request->nama;
        //$tgllahir = $request->tgllhr;

        //$jk = $request->gender;
        $nohp = $request->nohp;
        $email = $request->email;
        // $foto = File::get($request->image);
        if ($request->image == null) {
            $foto = "0";
        } else {
            // $foto = "Foto ada";
            $foto = base64_encode(File::get($request->image));
        }
        // dd($foto);

        // $foto = $request->file('image');
        //$namaortu = $request->namaortu;
        //$nohportu = $request->nohportu;

        // $alamat = $request->alamat;
        // $kelurahan = $request->kelurahan;
        // $kecamatan = $request->kecamatan;
        // $kotakab = $request->kotakab;
        // $provinsi = $request->provinsi;
        // $kodepos = $request->kodepos;

        // $tempatkuliahkerja = $request->tempatkuliahkerja;
        // $jurkuliah = $request->jurkuliah;

        if (isset($request->noktp2)) {
            $noktp2 = $request->noktp2;
            $nama2 = $request->nama2;
            //$tgllahir2 = $request->tgllhr2;

            //$jk2 = $request->gender2;
            $nohp2 = $request->nohp2;
            $email2 = $request->email2;
            // $foto2 = File::get($request->image2);
            if ($request->image2 == null) {
                $foto2 = "0";
            } else {
                // $foto = "Foto ada";
                $foto2 = base64_encode(File::get($request->image2));
            }
            // dd($foto);

            // $foto2 = $request->file('image2');
            //$namaortu2 = $request->namaortu2;
            //$nohportu2 = $request->nohportu2;

            // $alamat2 = $request->alamat2;
            // $kelurahan2 = $request->kelurahan2;
            // $kecamatan2 = $request->kecamatan2;
            // $kotakab2 = $reques2t->kotakab2;
            // $provinsi2 = $request->provinsi2;
            // $kodepos2 = $request->kodepos2;

            // $tempatkuliahkerja2 = $request->tempatkuliahkerja2;
            // $jurkuliah2 = $request->jurkuliah2;            
        }

        $kodekamar = $request->kodekamar;
        $nokamar = $request->nokamar;
        $kamarterisi = $request->kamarterisi;
        $hargakamar = $request->hargakamar;
        $periode = $request->periodebayar;
        $diskon = $request->diskon;
        $totalharga = $request->totalharga;
        $totalbayar = $request->totalbayar;
        // return redirect(route('admin.test', $foto));

        // $status = Input::get("cboxStat");
        $url = api_url('admin_penyewa-create.php');
        // $arr = json_decode($url, TRUE);
        // $json = json_encode($arr);
        // print_r($foto);
        if (isset($request->noktp2)) {
            $data = array(
                '_kode' => $kode,
                '_noktp' => $noktp,
                '_nama' => $nama,
                // '_tgllahir' => $tgllahir,

                // '_jk' => $jk, 
                '_nohp' => $nohp,
                '_email' => $email,
                '_foto' => $foto,
                // '_namaortu' => $namaortu,
                // '_nohportu' => $nohportu,

                // '_alamat' => $alamat,
                // '_kelurahan' => $kelurahan, 
                // '_kecamatan' => $kecamatan,
                // '_kotakab' => $kotakab,
                // '_provinsi' => $provinsi,
                // '_kodepos' => $kodepos,

                // '_tempatkuliahkerja' => $tempatkuliahkerja, 
                // '_jurkuliah' => $jurkuliah,

                '_kode' => $kode,
                '_noktp2' => $noktp2,
                '_nama2' => $nama2,
                // '_tgllahir2' => $tgllahir2,

                // '_jk2' => $jk2, 
                '_nohp2' => $nohp2,
                '_email2' => $email2,
                '_foto2' => $foto2,
                // '_namaortu2' => $namaortu2,
                // '_nohportu2' => $nohportu2,

                // '_alamat2' => $alamat2,
                // '_kelurahan2' => $kelurahan2, 
                // '_kecamatan2' => $kecamatan2,
                // '_kotakab2' => $kotakab2,
                // '_provinsi2' => $provinsi2,
                // '_kodepos2' => $kodepos2,

                // '_tempatkuliahkerja2' => $tempatkuliahkerja2, 
                // '_jurkuliah2' => $jurkuliah2,

                '_kodekamar' => $kodekamar,
                '_nokamar' => $nokamar,
                '_kamarterisi' => $kamarterisi,
                '_hargakamar' => $hargakamar,
                '_periodebayar' => $periode,
                '_diskon' => $diskon,
                '_totalharga' => $totalharga,
                '_totalbayar' => $totalbayar,

                '_kodeadmin' => Auth::user()->kode,
                '_namaadmin' => Auth::user()->nama,
                '_kodekost' => $kodekost,
            );
        } else {
            $data = array(
                '_kode' => $kode,
                '_noktp' => $noktp,
                '_nama' => $nama,
                // '_tgllahir' => $tgllahir,

                // '_jk' => $jk, 
                '_nohp' => $nohp,
                '_email' => $email,
                '_foto' => $foto,
                // '_namaortu' => $namaortu,
                // '_nohportu' => $nohportu,

                // '_alamat' => $alamat,
                // '_kelurahan' => $kelurahan, 
                // '_kecamatan' => $kecamatan,
                // '_kotakab' => $kotakab,
                // '_provinsi' => $provinsi,
                // '_kodepos' => $kodepos,

                // '_tempatkuliahkerja' => $tempatkuliahkerja, 
                // '_jurkuliah' => $jurkuliah,
                '_kodekamar' => $kodekamar,
                '_nokamar' => $nokamar,
                '_kamarterisi' => $kamarterisi,
                '_hargakamar' => $hargakamar,
                '_periodebayar' => $periode,
                '_diskon' => $diskon,
                '_totalharga' => $totalharga,
                '_totalbayar' => $totalbayar,

                '_kodeadmin' => Auth::user()->kode,
                '_namaadmin' => Auth::user()->nama,
                '_kodekost' => $kodekost,
            );
        }
        // return redirect(route('admin.test', $data['_foto']));
        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $result = api_post($url, $data);
        // return $result;
        if ($result == '{"daftarpenyewa":"penyewaterdaftar"}') {
            Session::flash('message', 'Penyewa baru telah berhasil ditambahkan silahkan cek email untuk mendapatkan username dan password');
            return redirect(route('admin.penyewa'));
        } else {
            Session::flash('message', 'Gagal menambahkan penyewa baru "ERROR : ' . $result . '"');
            return redirect(route('admin.createpenyewaview'));
        }
        // dd($request->file('image'));
        // return $request->nama;
    }

    public function viewEditPenyewa($id)
    {

        $kode = $id;
        $url = api_url('admin_penyewa-get-list.php');
        $url2 = api_url('wil_get-provinsi.php');
        $data = array(
            '_kodekost' => Auth::user()->adminkost[$this->index]->kode_kost
        );
        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $result = api_post($url, $data);
        $result2 = api_get($url2);
        $json = json_decode($result, true);
        $json2 = json_decode($result2, true);
        // $kode = $id;
        // $url = api_url('admin_getdatapenyewa-getprov.php');
        // $json = json_decode(api_get($url), true);
        $data = [
            'pageTitle' => 'Admin Edit Penyewa',
            'activeId' => 'penyewa',
            'activeIdSubMenu' => '',
        ];
        // dd($json);
        if ($json['datapenyewa'] != null) {
            return view('admin.update-penyewa', ['penyewa' => $json, 'provinsi' => $json2], compact('data', 'kode'));
        } else {
            return redirect()->route('admin.penyewa');
        }
    }

    public function updatePenyewa($id, Request $request)
    {
        $url = api_url('admin_penyewa-update.php');
        if ($request->ed == "off") {
            if ($request->hasFile('foto')) {
                $foto = base64_encode(File::get($request->foto));
            } else {
                $foto = "0";
            }
            $kode = $id;
            $nama = $request->nama;
            $tgllhr = $request->tgllhr;
            $nohp = $request->nohp;
            $jk = $request->gender;
            $email = $request->email;

            $namaortu = $request->namaortu;
            $nohportu = $request->nohportu;
            $tempatkuliahkerja = $request->tempatkuliahkerja;
            $jurkuliah = $request->jurkuliah;
            $alamat = $request->alamat;
            $status = $request->status;
            $data = array(
                '_du' => 'off',
                '_kode' => $kode,
                '_nama' => $nama,
                '_tgllahir' => $tgllhr,
                '_jk' => $jk,
                '_nohp' => $nohp,
                '_email' => $email,
                '_foto' => $foto,
                '_namaortu' => $namaortu,
                '_nohportu' => $nohportu,
                '_alamat' => $alamat,
                '_kelurahan' => $request->kelurahan,
                '_kecamatan' => $request->kecamatan,
                '_kotakab' => $request->kotakab,
                '_provinsi' => $request->provinsi,
                '_kodepos' => $request->kodepos,
                '_tempatkuliahkerja' => $tempatkuliahkerja,
                '_jurkuliah' => $jurkuliah,
                '_status' => $status,
                '_kodeadmin' => Auth::user()->kode,
                '_namaadmin' => Auth::user()->nama,
                '_kodekost' => Auth::user()->adminkost[$this->index]->kode_kost
            );
            $option = array(
                'http' => array(
                    'header'  => api_header(),
                    'method' => 'POST',
                    'content' => http_build_query($data)
                )
            );
            $context = stream_context_create($option);
            $result = api_post($url, $data);

            if (strpos($result, 'berhasil update penyewa') !== false) {
                Session::flash('message', 'Berhasil melakukan update penyewa');
                return redirect(route('admin.penyewa'));
            } else {
                Session::flash('message', 'Gagal mengupdate penyewa ERROR : ' . $result);
                return redirect(route('admin.penyewa'));
            }
        } else {
            if ($request->hasFile('foto')) {
                $foto = base64_encode(File::get($request->foto));
            } else {
                $foto = "0";
            }
            $kode = $id;
            $nama = $request->nama;
            $tgllhr = $request->tgllhr;
            $nohp = $request->nohp;
            $jk = $request->gender;
            $email = $request->email;

            $namaortu = $request->namaortu;
            $nohportu = $request->nohportu;
            $tempatkuliahkerja = $request->tempatkuliahkerja;
            $jurkuliah = $request->jurkuliah;
            $alamat = $request->alamat;
            $kelurahan = $request->kelurahan;
            $kecamatan = $request->kecamatan;
            $kotakab = $request->kotakab;
            $provinsi = $request->provinsi;
            $kodepos = $request->kodepos;
            $status = $request->status;
            $data = array(
                '_du' => 'on',
                '_kode' => $kode,
                '_nama' => $nama,
                '_tgllahir' => $tgllhr,
                '_jk' => $jk,
                '_nohp' => $nohp,
                '_email' => $email,
                '_foto' => $foto,
                '_namaortu' => $namaortu,
                '_nohportu' => $nohportu,
                '_alamat' => $alamat,
                '_kelurahan' => $kelurahan,
                '_kecamatan' => $kecamatan,
                '_kotakab' => $kotakab,
                '_provinsi' => $provinsi,
                '_kodepos' => $kodepos,
                '_tempatkuliahkerja' => $tempatkuliahkerja,
                '_jurkuliah' => $jurkuliah,
                '_status' => $status,
                '_kodeadmin' => Auth::user()->kode,
                '_namaadmin' => Auth::user()->nama,
                '_kodekost' => Auth::user()->adminkost[$this->index]->kode_kost
            );
            $option = array(
                'http' => array(
                    'header'  => api_header(),
                    'method' => 'POST',
                    'content' => http_build_query($data)
                )
            );
            $result = api_post($url, $data);

            if (strpos($result, 'berhasil update penyewa') !== false) {
                Session::flash('message', 'Berhasil melakukan update penyewa');
                return redirect(route('admin.penyewa'));
            } else {
                Session::flash('message', 'Gagal mengupdate penyewa');
                return redirect(route('admin.penyewa'));
            }
        }
    }

    public function getDataKamarPindah(Request $request)
    {
        $kodekamar = $request->nilai1;
        $jumlahpenyewa = $request->nilai2;
        $url = api_url('admin_penyewa-pindah-getkamartujuan.php');
        $data = array(
            '_kodekamar' => $kodekamar,
            '_jmlpenyewa' => $jumlahpenyewa,
            '_kodekost' => Auth::user()->adminkost[$this->index]->kode_kost
        );
        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $result = api_post($url, $data);

        return $result;
    }

    /* --------------------------------------------------------------------------------------- */
    /* ------------------------------------- PENGUMUMAN -------------------------------------- */
    /* --------------------------------------------------------------------------------------- */

    public function pengumuman()
    {
        $url = api_url('admin_pengumuman_getdata.php');

        $data = array(
            '_kodekost' => Auth::user()->adminkost[$this->index]->kode_kost
        );

        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $result = api_post($url, $data);

        $json = json_decode($result, true);

        $data = [
            'pageTitle' => 'Admin Pengumuman',
            'activeId' => 'pengumuman',
            'activeIdSubMenu' => '',

        ];
        // dd($json);
        return view('admin.pengumuman', ['berita' => $json], compact('data'));
    }

    public function createPengumuman(Request $request)
    {
        $judul = $request->judul;
        $isi = $request->isi;
        $status = $request->status;
        $kodekost = Auth::user()->adminkost[$this->index]->kode_kost;
        $url = api_url('admin_pengumuman_tambah.php');
        $data = array(
            '_judul' => $judul,
            '_berita' => $isi,
            '_status' => $status,
            '_kodeadmin' => Auth::user()->kode,
            '_namaadmin' => Auth::user()->nama,
            '_kodekost' => $kodekost
        );
        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $result = api_post($url, $data);
        //{"pengumuman":"databerhasilditambah"}
        if ($result == '{"pengumuman":"databerhasilditambah"}') {
            Session::flash('message', 'Pengumuman berhasil ditambahkan');
            return redirect(route('admin.pengumuman'));
        } else {
            Session::flash('message', 'Pengumuman tidak berhasil ditambahkan ERROR : ' . $result);
            return redirect(route('admin.pengumuman'));
        }
        return $result;
    }

    public function detailPengumuman($id)
    {
        $url = api_url('admin_pengumuman_getdata.php');
        $data = array(
            '_kodekost' => Auth::user()->adminkost[$this->index]->kode_kost
        );

        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $result = api_post($url, $data);

        $json = json_decode($result, true);
        $kode = $id;
        $data = [
            'pageTitle' => 'Admin Detail Pengumuman',
            'activeId' => 'pengumuman',
            'activeIdSubMenu' => '',

        ];
        if ($json['pengumuman'] != "error") {
            return view('admin.detail-pengumuman', ['berita' => $json], compact('data', 'kode'));
        } else {
            return redirect()->route('admin.pengumuman');
        }
    }

    public function editPengumuman(Request $request)
    {
        $url = api_url('admin_pengumuman_ubah.php');
        $kode = $request->kode;
        $judul = $request->judul;
        $isi = $request->berita;
        $status = $request->status;
        $data = array(
            '_kode' => $kode,
            '_judul' => $judul,
            '_berita' => $isi,
            '_status' => $status,
            '_kodeadmin' => Auth::user()->kode,
            '_namaadmin' => Auth::user()->nama,
            '_kodekost' => Auth::user()->adminkost[$this->index]->kode_kost
        );
        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $result = api_post($url, $data);

        if ($result == '{"pengumuman":"updateberhasil"}') {
            Session::flash('message', 'Berhasil melakukan perubahan pada pengumuman');
            return redirect(route('admin.pengumuman'));
        } else {
            Session::flash('message', 'Gagal melakukan perubahan pada pengumuman');
            return redirect(route('admin.pengumuman'));
        }
    }

    public function pembayaran()
    {
        $url = api_url('admin_kamar-get-terisi.php');

        $data = array(
            '_kodekost' => Auth::user()->adminkost[$this->index]->kode_kost
        );

        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $result = api_post($url, $data);

        $json = json_decode($result, true);

        $data = [
            'pageTitle' => 'Admin Bayar Kos',
            'activeId' => 'pemasukan',
            'activeIdSubMenu' => 'bayar-kos',
        ];

        return view('admin.pembayaran', ['kamar' => $json], compact('data'));
    }

    public function datasewa(Request $request)
    {
        $kodekamar = $request->kodekamar;
        $nokamar   = $request->nokamar;
        $url = api_url('admin_pendapatan-get-datasewa.php');
        $data = array(
            '_kodekamar' => $kodekamar,
            '_nokamar'   => $nokamar,
            '_kodekost'  => Auth::user()->adminkost[$this->index]->kode_kost
        );

        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $result = api_post($url, $data);

        return $result;
    }

    public function lp()
    {
        return view('admin.laporan-keuangan');
    }

    public function inputBayar(Request $request)
    {
        $kodeSewa = $request->kodesewa;
        $kodekamar = $request->kode_kamar;
        $nokamar = $request->nokamar;
        $tglPembayaran = $request->tglpembayaran;
        $periodeBayar = $request->periodebayar;
        $hargaPerbulans = $request->hargaperbulan;
        $hargaPerbulan = str_replace(',', '', $hargaPerbulans);
        $dendas = $request->denda;
        $denda = str_replace(',', '', $dendas);
        $denda_prev = $request->denda_sebelumnya;
        $diskons = $request->diskon;
        $diskon = str_replace(',', '', $diskons);
        $diskon_prev = $request->diskon_sebelumnya;
        $harga_pindah_prev = $request->harga_pindah_sebelumnya;
        $total_bayar_prev = $request->total_bayar_sebelumnya;
        $sisa_bayar = $request->sisa_bayar;
        $totalHargas = $request->hargatotal;
        $totalHarga = str_replace(',', '', $totalHargas);
        $total_harga_prev = $request->total_harga_sebelumnya;
        $totalBayars = $request->totalbayar;
        $totalBayar = str_replace(',', '', $totalBayars);
        $metode = $request->metode_bayar;
        $url = api_url('admin_pendapatan-input.php');
        $data = array(
            '_kode_sewa' => $kodeSewa,
            '_kodekamar' => $kodekamar,
            '_nokamar' => $nokamar,
            '_tanggal_pembayaran' => $tglPembayaran,
            '_periode_bayar' => $periodeBayar,
            '_harga_perbulan' => $hargaPerbulan,
            'denda' => $denda,
            '_denda_sebelumnya' => $denda_prev,
            'diskon' => $diskon,
            '_diskon_sebelumnya' => $diskon_prev,
            '_harga_pindah_sebelumnya' => $harga_pindah_prev,
            '_total_bayar_sebelumnya' => $total_bayar_prev,
            '_sisa_bayar' => $sisa_bayar,
            '_total_harga' => $totalHarga,
            '_total_harga_sebelumnya' => $total_harga_prev,
            '_total_bayar' => $totalBayar,
            '_metode' => $metode,
            '_kodeadmin' => Auth::user()->kode,
            '_namaadmin' => Auth::user()->nama,
            '_kodekost' => Auth::user()->adminkost[$this->index]->kode_kost
        );
        // dd($data);
        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $result = api_post($url, $data);
        // return $result;
        if ($result == '{"datapembayaran":"data pembayaran berhasil diinput"}') {
            Session::flash('message', 'Pembayaran Berhasil');
            return redirect(route('admin.pembayaran'));
        } else {
            Session::flash('message', 'Pembayaran Gagal ERROR : ' . $result);
            return redirect(route('admin.pembayaran'));
        }
    }

    public function viewPendapatanLL()
    {
        $data = [
            'pageTitle' => 'Admin Input Pendapatan Lain-lain',
            'activeId' => 'pemasukan',
            'activeIdSubMenu' => 'pendapatanll'
        ];

        return view('admin.pendapatan-lain-lain', compact('data'));
    }

    public function pendapatanLainlain(Request $request)
    {
        $url = api_url('admin_pendapatan-lainnya-input.php');

        $data = array(
            '_keterangan' => $request->keterangan,
            '_nominal' => $request->ttl,
            '_metode' => $request->metode,
            '_kodeadmin' => Auth::user()->kode,
            '_namaadmin' => Auth::user()->nama,
            '_kodekost' => Auth::user()->adminkost[$this->index]->kode_kost
        );

        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $result = api_post($url, $data);
        $data = [
            'pageTitle' => 'Admin Input Pendapatan Lain-lain',
            'activeId' => 'pemasukan',
            'activeIdSubMenu' => 'pendapatanll'
        ];
        // return $result;
        if ($result = 'Auth::user()->adminkost[$this->index]->kode_kost') {
            Session::flash('message', 'Berhasil melakukan input pendapatan lain-lain');
            return redirect(route('admin.viewpendpatanll'));
        } else {
            Session::flash('message', 'Gagal melakukan input pendapatan lain-lain');
            return redirect(route('admin.viewpendpatanll'));
        }
    }

    public function viewPengeluaran()
    {
        $data = [
            'pageTitle' => 'Admin Input Pengeluaran',
            'activeId' => 'pengeluaran',
            'activeIdSubMenu' => 'pengeluaran',

        ];
        $url = api_url('admin_pengeluaran-get-jenis.php');
        $json = json_decode(api_get($url), true);
        if (!is_array($json)) {
            $json = array('jenispengeluaran' => array());
        }
        if (!isset($json['jenispengeluaran']) || !is_array($json['jenispengeluaran'])) {
            $json['jenispengeluaran'] = array();
        }

        return view('admin.pengeluaran', ['keluar' => $json], compact('data'));
    }

    public function inputPengeluaran(Request $request)
    {
        if ($request->jenispengeluaran == "4-2100") {
            $keterangan = $request->keterangan;
        } else {
            $keterangan = $request->textpengeluaran;
        }
        $nominal = $request->ttl;
        $metode = $request->metode;
        $kodeakun = $request->jenispengeluaran;
        $kodekost = Auth::user()->adminkost[$this->index]->kode_kost;

        $url = api_url('admin_pengeluaran-input.php');

        $data = array(
            '_keterangan' => $keterangan,
            '_nominal' => $nominal,
            '_metode' => $metode,
            '_kodeakun' => $kodeakun,
            '_kodeadmin' => Auth::user()->kode,
            '_namaadmin' => Auth::user()->nama,
            '_kodekost' => $kodekost,
        );

        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $result = api_post($url, $data);

        // return $result;

        if ($result == '{"datapengeluaran":"data pembayaran berhasil diinput"}') {
            Session::flash('message', 'Berhasil melakukan input pengeluaran');
            return redirect(route('admin.pengeluaran'));
        } else {
            Session::flash('message', 'Gagal melakukan input pengeluaran ERROR : ' . $result);
            return redirect(route('admin.pengeluaran'));
        }
    }

    public function informasisewa()
    {
        $url = api_url('admin_informasisewa-get-data.php');

        $data = array(
            '_kodekost' => Auth::user()->adminkost[$this->index]->kode_kost
        );

        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $result = api_post($url, $data);

        $json = json_decode($result, true);

        $data = [
            'pageTitle' => 'Admin Informasi Sewa',
            'activeId' => 'informasisewa',
        ];
        //dd($json);
        return view('admin.informasi-sewa', ['info' => $json], compact('data'));
    }

    public function informasisewadetail(Request $request)
    {
        $url = api_url('admin_informasisewa-get-detail.php');

        $data = array(
            '_kodekamar'    => $request->kode,
            '_kodekost'     => Auth::user()->adminkost[$this->index]->kode_kost
        );

        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $result = api_post($url, $data);

        return $result;
    }
}
