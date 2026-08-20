<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;

class PenyewaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:penyewa');
    }

    public function dashboard()
    {
        View::share('appName', Auth::user()->kost->nama_kost);

        $kode = Auth::user()->kode;
        $pin = Auth::user()->nomorpin;
        $url = api_url('login_ph.php');

        $data = array(
            'kode' => $kode,
            'pin' => $pin
        );

        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context = stream_context_create($option);
        $result = file_get_contents($url, false, $context);
        $json = json_decode($result, true);

        $data = [
            'pageTitle' => 'Biodata Penyewa',
            'activeId' => 'biodata'
        ];

        return view('penyewa.biodata', ['bio' => $json], compact('data'));
    }

    public function getKomenKeluhan()
    {
        $url = api_url('keluhan_getdata.php');
        $kode = Auth::user()->kode;
        $pin = Auth::user()->nomorpin;
        $data = array(
            '_kode' => $kode,
            '_pin' => $pin
        );
        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context = stream_context_create($option);
        $result = file_get_contents($url, false, $context);
        $json = json_decode($result, true);
        return $result;
    }

    public function pembayaran()
    {
        View::share('appName', Auth::user()->kost->nama_kost);

        $kode = Auth::user()->kode;
        $pin = Auth::user()->nomorpin;
        $url = api_url('login_ph.php');

        $data = array(
            'kode' => $kode,
            'pin' => $pin
        );

        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context = stream_context_create($option);
        $result = file_get_contents($url, false, $context);
        $json = json_decode($result, true);

        $data = [
            'pageTitle' => 'Penyewa Pembayaran',
            'activeId' => 'pembayaran'
        ];

        return view('penyewa.pembayaran', ['bio' => $json], compact('data'));
    }
    public function riwayatpembayaran()
    {
        View::share('appName', Auth::user()->kost->nama_kost);

        $kode = Auth::user()->kode;
        $pin = Auth::user()->nomorpin;
        $url = api_url('penyewa_pembayaran_getdata.php');

        $data = array(
            '_kodepenyewa' => $kode,
            '_nomorpin' => $pin
        );

        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context = stream_context_create($option);
        $result = file_get_contents($url, false, $context);
        $json = json_decode($result, true);
        // dd($json);

        $data = [
            'pageTitle' => 'Riwayat Pembayaran',
            'activeId' => 'pembayaran'
        ];
        return view('penyewa.pembayaran-riwayat', ['dp' => $json], compact('data'));
    }

    public function pengumuman()
    {
        View::share('appName', Auth::user()->kost->nama_kost);

        $kode = Auth::user()->kode;
        $url = api_url('penyewa_pengumuman_getdata.php');

        $data = array(
            '_kode' => $kode,
        );

        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context = stream_context_create($option);
        $result = file_get_contents($url, false, $context);
        $json = json_decode($result, true);

        $data = [
            'pageTitle' => 'Pengumuman',
            'activeId' => 'pengumuman'
        ];
        // return dd($json);
        return view('penyewa.pengumuman',  ['pengumuman' => $json], compact('data'));
    }

    public function keluhan()
    {
        View::share('appName', Auth::user()->kost->nama_kost);

        $kode = Auth::user()->kode;
        $pin = Auth::user()->nomorpin;
        $url = api_url('keluhan_getdata.php');

        $data = array(
            '_kode' => $kode,
            '_pin' => $pin
        );

        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context = stream_context_create($option);
        $result = file_get_contents($url, false, $context);
        $json = json_decode($result, true);

        $data = [
            'pageTitle' => 'Keluhan',
            'activeId' => 'keluhan'
        ];
        // dd($json);
        // return $json;
        return view('penyewa.keluhan', ['keluhan' => $json], compact('data'));
    }
    public function keluhanbaru(Request $request)
    {
        $penyewa = Auth::user()->kode;
        $judul = $request->judul;
        $kategori = $request->kategori;
        $uraian = $request->uraian;

        $url = api_url('penyewa_keluhan_tambahdata.php');

        $data = array(
            '_penyewa' => $penyewa,
            '_judul' => $judul,
            '_kategori' => $kategori,
            '_uraian' => $uraian,
            '_foto' => '0'
        );

        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context = stream_context_create($option);
        $result = file_get_contents($url, false, $context);

        // return $result;
        if ($result == '{"insertkeluhanbaru":"inputkeluhansukes"}') {
            Session::flash('message', 'Berhasil menambahkan keluhan');
            return redirect(route('penyewa.keluhan'));
        } else {
            Session::flash('message', 'Gagal menambahkan keluhan');
            return redirect(route('penyewa.keluhan'));
        }
    }
    public function ubahpin(Request $request)
    {
        $kode = Auth::user()->kode;
        $pinlama = $request->pinlama;
        $pinbaru = $request->pinbaru;

        $url = api_url('penyewa_ubahpin.php');
        $data = array(
            '_pinlama' => $pinlama,
            '_pinbaru' => $pinbaru,
            '_kode' => $kode
        );

        $option = array(
            'http' => array(
                'header'  => api_header(),
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context = stream_context_create($option);
        $result = file_get_contents($url, false, $context);

        // return $result;
        if ($result == '{"ubahpin":"pin berhasil diubah"}') {
            Session::flash('message', 'Berhasil mengubah pin');
            return redirect(route('penyewa.dashboard'));
        } else {
            Session::flash('message', 'Gagal mengubah pin');
            return redirect(route('penyewa.dashboard'));
        }
    }
}
