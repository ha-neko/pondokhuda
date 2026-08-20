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

class OwnerController extends Controller
{
    protected $index;

    public function __construct()
    {
        $this->middleware('auth:owner');

        $this->middleware(function ($request, $next) {

            if (count(Auth::user()->ownerkost) > 0) {
                $this->index = Session::get('index');
                View::share('indexKost', $this->index);
                View::share('appName', Auth::user()->ownerkost[$this->index]->kost->nama_kost);
            } else {
                View::share('appName');
            }

            return $next($request);
        });
    }

    public function test()
    {
        $data = [
            'pageTitle'         => 'Test',
            'activeId'          => 'dashboard',
            'activeIdSubMenu'   => ''
        ];

        return view('owner.test', compact('data'));
    }

    public function firstTimeSetting()
    {
        $data = [
            'pageTitle'         => 'First Time Setting',
            'activeId'          => 'dashboard',
            'activeIdSubMenu'   => ''
        ];

        return view('owner.first-time-setting', compact('data'));
    }

    public function dashboard()
    {
        if (count(Auth::user()->ownerkost) > 0) {
            $url = api_url('get_report.php');
            $data = array(
                '_kodekost' => Auth::user()->ownerkost[$this->index]->kode_kost
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
                'pageTitle'         => 'Owner Dashboard ',
                'activeId'          => 'dashboard',
                'activeIdSubMenu'   => ''
            ];

            return view('owner.dashboard', ['report' => $json], compact('data'));
        } else {
            return redirect()->route('owner.first-time-setting');
        }
    }

    // --------------------------- KOST --------------------------- \\
    public function kostList()
    {
        $data = [
            'pageTitle'         => 'Daftar Kost',
            'activeId'          => 'kost',
            'activeIdSubMenu'   => ''
        ];

        return view('owner.kost.list', compact('data'));
    }

    public function kostDetail(Request $request)
    {
        $result[] = array(
            'logo'          => Auth::user()->ownerkost[$request->kode]['kost']['logo'],
            'namakost'      => Auth::user()->ownerkost[$request->kode]['kost']['nama_kost'],
            'alamatkost'    => Auth::user()->ownerkost[$request->kode]['kost']['alamat'],
            'emailkost'     => Auth::user()->ownerkost[$request->kode]['kost']['emailkost'],
            'namecolor'     => Auth::user()->ownerkost[$request->kode]['kost']['name_color'],
        );

        return json_encode(array('kost' => $result));;
    }

    public function kostAdd()
    {
        $data = [
            'pageTitle'         => 'Tambah Kost',
            'activeId'          => 'kost',
            'activeIdSubMenu'   => ''
        ];

        return view('owner.kost.add', compact('data'));
    }

    public function kostEdit($id)
    {
        $data = [
            'pageTitle'         => 'Edit Kost',
            'activeId'          => 'kost',
            'activeIdSubMenu'   => ''
        ];

        return view('owner.kost.edit', compact('data', 'id'));
    }

    public function kostUpdate(Request $request, $id)
    {
        $url = api_url('owner_kost-update.php');
        $logo = "noimage";
        if ($request->hasFile('logo')) {
            $logo = base64_encode(File::get($request->logo));
        }

        $data = array(
            '_kodekost'     => $id,
            '_namakost'     => $request->namakost,
            '_alamatkost'   => $request->alamatkost,
            '_emailkost'    => $request->emailkost,
            '_primarycolor' => $request->primarycolor,
            '_namecolor'    => $request->namecolor,
            '_logo'         => $logo,
            '_kodeowner' => Auth::user()->kode,
            '_namaowner' => Auth::user()->nama,
        );
        // dd($logo);
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
        // return $result;
        if ($json['kost'] == "kost berhasil diupdate") {
            Session::flash('alert-class', 'alert-success');
            Session::flash('message', $json['kost']);
        } else {
            Session::flash('alert-class', 'alert-danger');
            Session::flash('message', $result);
        }

        return redirect()->route('owner.kost');
    }

    public function kostCreate(Request $request)
    {
        $url = api_url('owner_kost-create.php');
        $logo = "noimage";
        if ($request->hasFile('logo')) {
            $logo = base64_encode(File::get($request->logo));
        }

        $data = array(
            '_namakost'     => $request->namakost,
            '_alamatkost'   => $request->alamatkost,
            '_emailkost'    => $request->emailkost,
            '_kodeowner'    => Auth::user()->kode,
            '_primarycolor' => $request->primarycolor,
            '_namecolor'    => $request->namecolor,
            '_logo'         => $logo,
            '_kodeowner' => Auth::user()->kode,
            '_namaowner' => Auth::user()->nama,
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

        if ($json['kost'] == "kost berhasil ditambah") {
            Session::flash('alert-class', 'alert-success');
            Session::flash('message', $json['kost']);
        } else {
            Session::flash('alert-class', 'alert-danger');
            Session::flash('message', $result);
        }
        if (count(Auth::user()->ownerkost) > 1) {
            return redirect()->route('owner.kost');
        } else {
            return redirect()->route('owner.dashboard');
        }
    }

    public function kostGanti($index)
    {
        $this->index = $index;

        Session::put('index', $this->index);

        return redirect()->back();
    }

    // --------------------------- END OF KOST --------------------------- \\

    // --------------------------- ADMIN --------------------------- \\
    public function adminList()
    {
        $url = api_url('owner_admin-get-list.php');
        $data = array(
            '_kode' => Auth::user()->kode,
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
            'pageTitle'         => 'Daftar Admin',
            'activeId'          => 'admin',
            'activeIdSubMenu'   => ''
        ];

        //dd($json);
        return view('owner.admin.list', ['admins' => $json], compact('data'));
    }

    public function adminDetail(Request $request)
    {
        $url = api_url('owner_admin-get-detail.php');
        $data = array(
            '_kodeadmin' => $request->kode,
            '_kodekost' => Auth::user()->ownerkost[$this->index]->kode_kost
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

        return $result;
    }

    public function adminAdd()
    {
        $data = [
            'pageTitle'       => 'Tambah Admin',
            'activeId'        => 'admin',
            'activeIdSubMenu' => ''
        ];

        return view('owner.admin.add', compact('data'));
    }

    public function adminCreate(Request $request)
    {
        $url = api_url('owner_admin-create.php');

        if ($request->foto == null) {
            $foto = "0";
        } else {
            $foto = base64_encode(File::get($request->foto));
        }

        if ($request->kost == null) {
            $kost = "0";
        } else {
            $kost = $request->kost;
        }

        $data = array(
            '_kode'      => $request->kode,
            '_nama'      => $request->nama,
            '_pin'       => $request->pin,
            '_notelp'    => $request->notelp,
            '_foto'      => $foto,
            '_kodeowner' => Auth::user()->kode,
            '_namaowner' => Auth::user()->nama,
            '_kodekost'  => $kost,
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
        //dd($json);
        if ($json['admin'][0]['admin'] == "admin berhasil ditambah") {
            Session::flash('alert-class', 'alert-success');
            Session::flash('message', $json['admin'][0]['admin'] . ' dengan kode: ' . $json['admin'][0]['kode'] . ' dan pin: ' . $json['admin'][0]['pin']);
        } else {
            Session::flash('alert-class', 'alert-danger');
            Session::flash('message', $result);
        }

        return redirect()->route('owner.admin');
    }

    public function adminEdit($kode)
    {
        $url = api_url('owner_admin-get-detail.php');
        $data = array(
            '_kodeadmin' => $kode,
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
            'pageTitle'       => 'Edit Admin',
            'activeId'        => 'admin',
            'activeIdSubMenu' => ''
        ];

        //dd($json);
        return view('owner.admin.edit', ['admin' => $json], compact('data'));
    }

    public function adminUpdate(Request $request)
    {
        //dd($request);
        $url = api_url('owner_admin-update.php');

        if ($request->foto == null) {
            $foto = "0";
        } else {
            $foto = base64_encode(File::get($request->foto));
        }

        if ($request->kost == null) {
            $kost = "0";
        } else {
            $kost = $request->kost;
        }

        if ($request->listkost[0] == null) {
            $listkost = "0";
        } else {
            $listkost = $request->listkost;
        }

        $data = array(
            '_kode'     => $request->kode,
            '_nama'     => $request->nama,
            '_pin'      => $request->pin,
            '_notelp'   => $request->notelp,
            '_foto'     => $foto,
            '_kodekost' => $kost,
            '_listkost' => $listkost,
            '_kodeowner' => Auth::user()->kode,
            '_namaowner' => Auth::user()->nama,
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

        if ($json['admin'] == "admin berhasil diubah") {
            Session::flash('alert-class', 'alert-success');
            Session::flash('message', $json['admin']);
        } else {
            Session::flash('alert-class', 'alert-danger');
            Session::flash('message', $result);
        }

        return redirect()->route('owner.admin');
    }
    // --------------------------- END OF ADMIN --------------------------- \\


    // --------------------------- PENYEWA --------------------------- \\
    public function penyewaList()
    {
        $url = api_url('owner_penyewa-get-list.php');
        $data = array(
            '_kodekost' => Auth::user()->ownerkost[$this->index]->kode_kost
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
            'pageTitle'       => 'Daftar Penyewa',
            'activeId'        => 'penyewa',
            'activeIdSubMenu' => ''
        ];

        //dd($json);
        return view('owner.penyewa.list', ['penyewas' => $json], compact('data'));
    }

    public function tambahPenyewa()
    {
        $url = api_url('admin_penyewa-create-getkamar.php');
        $url2 = api_url('wil_get-provinsi.php');
        $data = array(
            '_kodekost'     => Auth::user()->ownerkost[$this->index]->kode_kost,
            '_jmlpenyewa'   => '1'
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
        $result2 = file_get_contents($url2, false);
        $json = json_decode($result, true);
        $json2 = json_decode($result2, true);
        // dd($json);
        $data = [
            'pageTitle' => 'Owner Tambah Penyewa',
            'activeId' => 'penyewa',
            'activeIdSubMenu' => '',
        ];
        // dd($result2);
        return view('owner.penyewa.create', ['kamar' => $json, 'provinsi' => $json2], compact('data'));
    }
    public function createPenyewa(Request $request)
    {
        $kodekost = Auth::user()->ownerkost[$this->index]->kode_kost;
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

                '_kodeowner'    => Auth::user()->kode,
                '_namaowner'    => Auth::user()->nama,
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

                '_kodeowner'    => Auth::user()->kode,
                '_namaowner'    => Auth::user()->nama,
                '_kodekost' => $kodekost,
            );
        }
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
        if ($result == '{"daftarpenyewa":"penyewaterdaftar"}') {
            Session::flash('alert-class', 'alert-success');
            Session::flash('message', 'Penyewa baru telah berhasil ditambahkan silahkan cek email untuk mendapatkan username dan password');
            return redirect(route('owner.penyewa'));
        } else {
            Session::flash('alert-class', 'alert-danger');
            Session::flash('message', 'Gagal menambahkan penyewa baru "ERROR : ' . $result . '"');
            return redirect(route('owner.tambahpenyewa'));
        }
    }

    public function editPenyewa($id)
    {
        $kode = $id;
        $url = api_url('admin_penyewa-get-list.php');
        $url2 = api_url('wil_get-provinsi.php');
        $data = array(
            '_kodekost' => Auth::user()->ownerkost[$this->index]->kode_kost
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
        $result2 = file_get_contents($url2, false);
        $json = json_decode($result, true);
        $json2 = json_decode($result2, true);
        // $kode = $id;
        // $url = api_url('admin_getdatapenyewa-getprov.php');
        // $json = json_decode(file_get_contents($url), true);
        $data = [
            'pageTitle' => 'Owner Edit Penyewa',
            'activeId' => 'penyewa',
            'activeIdSubMenu' => '',
        ];
        // dd($json);
        if ($json['datapenyewa'] != null) {
            return view('owner.penyewa.edit', ['penyewa' => $json, 'provinsi' => $json2], compact('data', 'kode'));
        } else {
            return redirect()->route('owner.penyewa');
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
                '_kodeowner' => Auth::user()->kode,
                '_namaowner' => Auth::user()->nama,
                '_kodekost' => Auth::user()->ownerkost[$this->index]->kode_kost
            );
            $option = array(
                'http' => array(
                    'header'  => api_header(),
                    'method' => 'POST',
                    'content' => http_build_query($data)
                )
            );
            $context = stream_context_create($option);
            $result = file_get_contents($url, true, $context);

            if (strpos($result, 'berhasil update penyewa') !== false) {
                Session::flash('alert-class', 'alert-success');
                Session::flash('message', 'Berhasil melakukan update penyewa');
                return redirect(route('owner.penyewa'));
            } else {
                Session::flash('alert-class', 'alert-danger');
                Session::flash('message', 'Gagal mengupdate penyewa ERROR : ' . $result);
                return redirect(route('owner.penyewa'));
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
                '_kodeowner' => Auth::user()->kode,
                '_namaowner' => Auth::user()->nama,
                '_kodekost' => Auth::user()->ownerkost[$this->index]->kode_kost
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

            if (strpos($result, 'berhasil update penyewa') !== false) {
                Session::flash('alert-class', 'alert-success');
                Session::flash('message', 'Berhasil melakukan update penyewa');
                return redirect(route('owner.penyewa'));
            } else {
                Session::flash('alert-class', 'alert-danger');
                Session::flash('message', 'Gagal mengupdate penyewa');
                return redirect(route('owner.penyewa'));
            }
        }
    }

    public function getKamarPenyewa(Request $request)
    {
        $jumlahpenyewa = $request->jmlpenyewa;
        $url = api_url('admin_penyewa-create-getkamar.php');
        $data = array(
            '_kodekost'     => Auth::user()->ownerkost[$this->index]->kode_kost,
            '_jmlpenyewa'   => $jumlahpenyewa
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

        return $result;
    }

    public function pindahPenyewa()
    {
        $url = api_url('admin_kamar-get-terisi.php');

        $data = array(
            '_kodekost' => Auth::user()->ownerkost[$this->index]->kode_kost
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
            'pageTitle' => 'Owner Penyewa Pindah Kamar',
            'activeId' => 'penyewa',
            'activeIdSubMenu' => '',
        ];
        return view('owner.penyewa.pindah', ['nokamar' => $json], compact('data'));
    }

    public function getDataSewaKamar(Request $request)
    {
        $url = api_url('admin_penyewa-pindah-get-datasewa.php');
        $kodekamar = $request->nilai;
        $data = array(
            '_kodekamar' => $kodekamar,
            '_kodekost' => Auth::user()->ownerkost[$this->index]->kode_kost
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
        return $result;
    }

    public function getDataKamarPindah(Request $request)
    {
        $kodekamar = $request->nilai1;
        $jumlahpenyewa = $request->nilai2;
        $url = api_url('admin_penyewa-pindah-getkamartujuan.php');
        $data = array(
            '_kodekamar' => $kodekamar,
            '_jmlpenyewa' => $jumlahpenyewa,
            '_kodekost' => Auth::user()->ownerkost[$this->index]->kode_kost
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

        return $result;
    }

    public function hargaKamar(Request $request)
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
        $context = stream_context_create($option);
        $result = file_get_contents($url, false, $context);
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

        $kodekost = Auth::user()->ownerkost[$this->index]->kode_kost;

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

            '_kodeowner'    => Auth::user()->kode,
            '_namaowner'    => Auth::user()->nama,

            '_kodekost' => $kodekost
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
        if ($result == '{"datapindah":"penyewa berhasil pindah"}') {
            Session::flash('alert-class', 'alert-success');
            Session::flash('message', 'Berhasil memindahkan penyewa');
            return redirect(route('owner.penyewa'));
        } else {
            Session::flash('alert-class', 'alert-danger');
            Session::flash('message', 'Gagal memindahkan penyewa "ERROR : ' . $result . '"');
            return redirect(route('owner.pindahpenyewa'));
        }
    }

    public function berhentiSewa()
    {
        $url = api_url('admin_kamar-get-terisi.php');

        $data = array(
            '_kodekost' => Auth::user()->ownerkost[$this->index]->kode_kost
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
            'pageTitle' => 'Owner Penyewa Berhenti Sewa',
            'activeId' => 'penyewa',
            'activeIdSubMenu' => '',
        ];
        return view('owner.penyewa.berhenti', ['nokamar' => $json], compact('data'));
    }

    public function penyewaBerhentiSewa(Request $request)
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

            '_kodeowner'    => Auth::user()->kode,
            '_namaowner'    => Auth::user()->nama,
            '_kodekost'     => Auth::user()->ownerkost[$this->index]->kode_kost,
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

        // return $result;
        if ($result = '{"selesaisewa":"penyewa selesai menyewa"}') {
            Session::flash('alert-class', 'alert-success');
            Session::flash('message', 'Penyewa Telah Berhenti Sewa');
            return redirect(route('owner.penyewa'));
        } else {
            Session::flash('alert-class', 'alert-danger');
            Session::flash('message', 'Gagal Berhenti Sewa ' . $result);
            return redirect(route('owner.penyewa'));
        }
    }

    public function penyewaDetail(Request $request)
    {
        $url = api_url('owner_penyewa-get-detail.php');
        $data = array(
            '_kode' => $request->kode,
            '_kodekost' => Auth::user()->ownerkost[$this->index]->kode_kost
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

        return $result;
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
        $context = stream_context_create($option);
        $result = file_get_contents($url, false, $context);

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
        $context = stream_context_create($option);
        $result = file_get_contents($url, false, $context);

        return $result;
    }

    public function penyewaLaporan()
    {
        $url = api_url('get_report.php');
        $json = json_decode(file_get_contents($url), true);

        $data = [
            'pageTitle'       => 'Laporan Data Penyewa',
            'activeId'        => 'penyewa',
            'activeIdSubMenu' => ''
        ];

        //dd($json);
        return view('owner.penyewalaporan', ['reportPenyewas' => $json], compact('data'));
    }
    // --------------------------- END OF PENYEWA --------------------------- \\

    // --------------------------- KAMAR --------------------------- \\
    public function kamarList()
    {
        $url = api_url('owner_kamar-get-list.php');
        $data = array(
            '_kodekost' => Auth::user()->ownerkost[$this->index]->kode_kost,
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
            'pageTitle'       => 'Daftar Kamar',
            'activeId'        => 'kamar',
            'activeIdSubMenu' => ''
        ];

        //dd($json);
        return view('owner.kamar.list', ['kamars' => $json], compact('data'));
    }

    public function kamarDetail(Request $request)
    {
        $url = api_url('owner_kamar-get-detail.php');
        $data = array(
            '_kodekamar' => $request->kode,
            '_kodekost' => Auth::user()->ownerkost[$this->index]->kode_kost
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

        return $result;
    }

    public function kamarAdd()
    {
        $url = api_url('owner_kamar-get-list.php');
        $data = array(
            '_kodekost' => Auth::user()->ownerkost[$this->index]->kode_kost,
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
            'pageTitle'         => 'Tambah Kamar',
            'activeId'          => 'kamar',
            'activeIdSubMenu'   => ''
        ];

        return view('owner.kamar.add', ['kamars' => $json], compact('data'));
    }

    public function kamarCreate(Request $request)
    {
        $url = api_url('owner_kamar-create.php');
        $data = array(
            '_nokamar'      => $request->nomorkamar,
            '_hargakamar'   => $request->hargakamar,
            '_kodeowner'    => Auth::user()->kode,
            '_namaowner'    => Auth::user()->nama,
            '_kodekost'     => Auth::user()->ownerkost[$this->index]->kode_kost
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

        if ($json['datakamar'] == "data berhasil ditambah") {
            Session::flash('alert-class', 'alert-success');
            Session::flash('message', $json['datakamar']);
        } else {
            Session::flash('alert-class', 'alert-danger');
            Session::flash('message', $json['datakamar']);
        }

        return redirect()->route('owner.kamar');
    }

    public function kamarEdit($id)
    {
        $url = api_url('owner_kamar-get-detail.php');
        $url2 = api_url('owner_kamar-get-list.php');
        $data = array(
            '_kodekamar' => $id,
            '_kodekost' => Auth::user()->ownerkost[$this->index]->kode_kost
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
        $result2 = file_get_contents($url2, false, $context);

        $data = [
            'pageTitle'       => 'Ubah Data Kamar',
            'activeId'        => 'kamar',
            'activeIdSubMenu' => ''
        ];

        $json = json_decode($result, true);
        $json2 = json_decode($result2, true);

        //dd($json, $json2);
        if ($json['datakamar'][0]['kode'] != null) {
            return view('owner.kamar.edit', ['kamar' => $json, 'kamars' => $json2], compact('data'));
        } else {
            return redirect()->route('owner.kamar');
        }
    }

    public function kamarUpdate(Request $request)
    {
        if ($request->hargadisewakan != null) {
            $hargadisewakan = $request->hargadisewakan;
        } else {
            $hargadisewakan = "";
        }

        $url = api_url('owner_kamar-update.php');
        $data = array(
            '_kodekamar' => $request->id,
            '_nokamar' => $request->nomorkamar,
            '_statuskamar' => $request->statuskamar,
            '_hargakamar' => $request->hargakamar,
            '_hargadisewakan' => $hargadisewakan,
            '_kodeowner' => Auth::user()->kode,
            '_namaowner' => Auth::user()->nama,
            '_kodekost' => Auth::user()->ownerkost[$this->index]->kode_kost,
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

        //dd($result);

        if ($json['datakamar'] == "data berhasil diubah") {
            Session::flash('alert-class', 'alert-success');
            Session::flash('message', $json['datakamar']);
        } else {
            Session::flash('alert-class', 'alert-danger');
            Session::flash('message', $json['datakamar']);
        }

        return redirect()->route('owner.kamar');
    }
    // --------------------------- END OF KAMAR --------------------------- \\

    // --------------------------- KELUHAN --------------------------- \\
    public function keluhan()
    {
        $url = api_url('keluhan_getdata.php');
        $data = array(
            '_kode' => Auth::user()->kode,
            '_pin'  => Auth::user()->pin,
            '_kodekost'  => Auth::user()->ownerkost[$this->index]->kode_kost,
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
        //dd($json);
        $data = [
            'pageTitle'       => 'Daftar Keluhan',
            'activeId'        => 'keluhan',
            'activeIdSubMenu' => ''
        ];

        //print_r($json);
        return view('owner.keluhan.list', ['keluhans' => $json], compact('data'));
    }

    public function keluhanDetail($id)
    {
        $url = api_url('keluhan_getdata.php');
        $kodes = $id;
        $data = array(
            '_kode' => Auth::user()->kode,
            '_pin' => Auth::user()->pin,
            '_kodekost'  => Auth::user()->ownerkost[$this->index]->kode_kost,
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
            'pageTitle'       => 'Owner Detail Keluhan',
            'activeId'        => 'keluhan',
            'activeIdSubMenu' => ''
        ];
        // dd($json);
        if ($json['keluhan'] != null) {
            return view('owner.keluhan.detail', ['keluhan' => $json], compact('data', 'kodes'));
        } else {
            return redirect()->route('owner.keluhan');
        }
    }
    public function getKomenKeluhan()
    {
        $url = api_url('keluhan_getdata.php');
        $kode = Auth::user()->kode;
        $pin = Auth::user()->pin;
        $data = array(
            '_kode' => $kode,
            '_pin' => $pin,
            '_kodekost' => Auth::user()->ownerkost[$this->index]->kode_kost
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
        $data = [
            'pageTitle' => 'Owner Keluhan',
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
            '_kodekeluhan' => $kodekel,
            '_komen' => $komen,
            '_kodeowner'    => Auth::user()->kode,
            '_namaowner'    => Auth::user()->nama,
            '_kodekost'     => Auth::user()->ownerkost[$this->index]->kode_kost,
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
        return $json;
    }
    public function editKeluhan($id)
    {
        $url = api_url('keluhan_getdata.php');
        $kode = Auth::user()->kode;
        $pin = Auth::user()->pin;
        $kodes = $id;
        $data = array(
            '_kode' => $kode,
            '_pin' => $pin,
            '_kodekost' => Auth::user()->ownerkost[$this->index]->kode_kost
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
            'pageTitle' => 'Owner Edit Keluhan',
            'activeId' => 'keluhan',
            'activeIdSubMenu' => '',

        ];
        //dd($json);
        if ($json['keluhan'] != null) {
            return view('owner.keluhan.edit', ['keluhan' => $json], compact('data', 'kodes'));
        } else {
            return redirect()->route('owner.keluhan');
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
                '_kodeowner'    => Auth::user()->kode,
                '_namaowner'    => Auth::user()->nama,
                '_kodekost'     => Auth::user()->ownerkost[$this->index]->kode_kost,
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
                '_kodeowner'    => Auth::user()->kode,
                '_namaowner'    => Auth::user()->nama,
                '_kodekost'     => Auth::user()->ownerkost[$this->index]->kode_kost,
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
    // --------------------------- END OF KELUHAN --------------------------- \\

    // --------------------------- PENGUMUMAN --------------------------- \\
    public function pengumuman()
    {
        $url = api_url('admin_pengumuman_getdata.php');
        $data = array(
            '_kodekost' => Auth::user()->ownerkost[$this->index]->kode_kost
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
            'pageTitle'       => 'Daftar Pengumuman',
            'activeId'        => 'pengumuman',
            'activeIdSubMenu' => ''
        ];

        //print_r($json);
        return view('owner.pengumuman.list', ['pengumumans' => $json], compact('data'));
    }

    public function pengumumanCreate(Request $request)
    {
        $judul = $request->judul;
        $isi = $request->isi;
        $status = $request->status;
        $kodekost = Auth::user()->ownerkost[$this->index]->kode_kost;
        $url = api_url('admin_pengumuman_tambah.php');
        $data = array(
            '_judul' => $judul,
            '_berita' => $isi,
            '_status' => $status,
            '_kodeowner' => Auth::user()->kode,
            '_namaowner' => Auth::user()->nama,
            '_kodekost' => $kodekost
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
        //{"pengumuman":"databerhasilditambah"}
        if ($result == '{"pengumuman":"databerhasilditambah"}') {
            Session::flash('message', 'Pengumuman berhasil ditambahkan');
            return redirect(route('owner.pengumuman'));
        } else {
            Session::flash('message', 'Pengumuman tidak berhasil ditambahkan ERROR : ' . $result);
            return redirect(route('owner.pengumuman'));
        }
        return $result;
    }

    public function pengumumanEdit($id)
    {
        $url = api_url('admin_pengumuman_getdata.php');
        $data = array(
            '_kodekost' => Auth::user()->ownerkost[$this->index]->kode_kost
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
        $kode = $id;
        $data = [
            'pageTitle' => 'Owner Detail Pengumuman',
            'activeId' => 'pengumuman',
            'activeIdSubMenu' => '',

        ];
        if ($json['pengumuman'] != "error") {
            return view('owner.pengumuman.edit', ['berita' => $json], compact('data', 'kode'));
        } else {
            return redirect()->route('owner.pengumuman');
        }
    }

    public function pengumumanUpdate(Request $request)
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
            '_kodeowner' => Auth::user()->kode,
            '_namaowner' => Auth::user()->nama,
            '_kodekost' => Auth::user()->ownerkost[$this->index]->kode_kost
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

        if ($result == '{"pengumuman":"updateberhasil"}') {
            Session::flash('message', 'Berhasil melakukan perubahan pada pengumuman');
            return redirect(route('owner.pengumuman'));
        } else {
            Session::flash('message', 'Gagal melakukan perubahan pada pengumuman');
            return redirect(route('owner.pengumuman'));
        }
    }
    // --------------------------- END OF PENGUMUMAN --------------------------- \\

    // --------------------------- LAPORAN KEUANGAN --------------------------- \\

    // --------------------------- LAPORAN KEUANGAN TRANSAKSI --------------------------- \\
    public function keuTransaksi()
    {
        $url = api_url('owner_keu-transaksi.php');
        $data = array(
            '_bulan' => date('m'),
            '_tahun' => date('Y'),
            '_kodekost' => Auth::user()->ownerkost[$this->index]->kode_kost
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
        //dd($json);

        $bulans = array(
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        );

        $tahuns = array();

        for ($i = 2019; $i <= date('Y'); $i++) {
            array_push($tahuns, $i);
        }

        $data = [
            'pageTitle'       => 'Laporan Keuangan (Transaksi)',
            'activeId'        => 'laporan-keuangan',
            'activeIdSubMenu' => '',
            'bulans'           => $bulans,
            'tahuns'           => $tahuns
        ];

        //print_r($json);
        return view('owner.laporan_keuangan.transaksi', ['transaksis' => $json], compact('data'));
    }

    public function getDataTransaksi(Request $request)
    {
        $url = api_url('owner_keu-transaksi.php');
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $kodekost = $request->kodekost;
        $data = array(
            '_bulan' => $bulan,
            '_tahun' => $tahun,
            '_kodekost' => $kodekost,
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

        return $result;
    }
    // --------------------------- END OF LAPORAN KEUANGAN TRANSAKSI --------------------------- \\

    // --------------------------- LAPORAN KEUANGAN LABA RUGI --------------------------- \\
    public function keuLabaRugi()
    {
        $url = api_url('owner_keu-labarugi.php');
        $data = array(
            '_bulan' => date('m'),
            '_tahun' => date('Y'),
            '_kodekost' => Auth::user()->ownerkost[$this->index]->kode_kost
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
        //dd($json);

        $bulans = array(
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        );

        $tahuns = array();

        for ($i = 2019; $i <= date('Y'); $i++) {
            array_push($tahuns, $i);
        }

        $data = [
            'pageTitle'       => 'Laporan Keuangan (Laba Rugi)',
            'activeId'        => 'laporan-keuangan',
            'activeIdSubMenu' => '',
            'bulans'          => $bulans,
            'tahuns'          => $tahuns
        ];


        return view('owner.laporan_keuangan.laba-rugi', ['labarugis' => $json], compact('data'));
    }

    public function getDataLabaRugi(Request $request)
    {
        $url = api_url('owner_keu-labarugi.php');
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $kodekost = $request->kodekost;
        $data = array(
            '_bulan' => $bulan,
            '_tahun' => $tahun,
            '_kodekost' => $kodekost,
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

        return $result;
    }
    // --------------------------- END OFLAPORAN KEUANGAN LABA RUGI --------------------------- \\

    // --------------------------- LAPORAN KEUANGAN ARUS KAS --------------------------- \\
    public function keuArusKas()
    {
        /*$url = api_url('owner_keu-aruskas.php');
        $data = array(
            '_bulan' => date('m'),
            '_tahun' => date('Y')
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

        $json = json_decode($result, true);*/
        //dd($json);

        $bulans = array(
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        );

        $tahuns = array();

        for ($i = 2019; $i <= date('Y'); $i++) {
            array_push($tahuns, $i);
        }

        $data = [
            'pageTitle'       => 'Laporan Keuangan (Arus Kas)',
            'activeId'        => 'laporan-keuangan',
            'activeIdSubMenu' => '',
            'bulans'          => $bulans,
            'tahuns'          => $tahuns
        ];


        return view('owner.laporan_keuangan.arus-kas', /*['aruskases' => $json],*/ compact('data'));
    }

    public function getDataArusKas(Request $request)
    {
        /*$url = api_url('owner_keu-aruskas.php');
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $data = array(
            '_bulan' => $bulan,
            '_tahun' => $tahun
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

        return $result;*/
    }
    // --------------------------- END OF LAPORAN KEUANGAN ARUS KAS --------------------------- \\

    public function keuPosisiKeu()
    {
        $url = api_url('owner_keu-posisikeu.php');
        $data = array(
            '_bulan' => date('m'),
            '_tahun' => date('Y')
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
        //dd($json);

        $bulans = array(
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        );

        $tahuns = array();

        for ($i = 2019; $i <= date('Y'); $i++) {
            array_push($tahuns, $i);
        }

        $data = [
            'pageTitle'       => 'Laporan Keuangan (Arus Kas)',
            'activeId'        => 'laporan-keuangan',
            'activeIdSubMenu' => '',
            'bulans'          => $bulans,
            'tahuns'          => $tahuns
        ];

        return view('owner.laporan_keuangan.posisi-keuangan', ['posisikeus' => $json], compact('data'));
    }
    // --------------------------- END OF LAPORAN KEUANGAN --------------------------- \\

    // --------------------------- PEMASUKAN --------------------------- \\
    public function pembayaran()
    {
        $url = api_url('admin_kamar-get-terisi.php');

        $data = array(
            '_kodekost' => Auth::user()->ownerkost[$this->index]->kode_kost
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
            'pageTitle' => 'Owner Bayar Kos',
            'activeId' => 'pemasukan',
            'activeIdSubMenu' => 'bayar-kos',
        ];

        return view('owner.pemasukan.bayar', ['kamar' => $json], compact('data'));
    }

    public function datasewa(Request $request)
    {
        $kodekamar = $request->kodekamar;
        $nokamar   = $request->nokamar;
        $url = api_url('admin_pendapatan-get-datasewa.php');
        $data = array(
            '_kodekamar' => $kodekamar,
            '_nokamar'   => $nokamar,
            '_kodekost'  => Auth::user()->ownerkost[$this->index]->kode_kost
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

        return $result;
    }

    public function bayar(Request $request)
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
            '_kodeowner'    => Auth::user()->kode,
            '_namaowner'    => Auth::user()->nama,
            '_kodekost' => Auth::user()->ownerkost[$this->index]->kode_kost
        );
        // dd($data);
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
        if ($result == '{"datapembayaran":"data pembayaran berhasil diinput"}') {
            Session::flash('message', 'Pembayaran Berhasil');
            return redirect(route('owner.pembayaran'));
        } else {
            Session::flash('message', 'Pembayaran Gagal ERROR : ' . $result);
            return redirect(route('owner.pembayaran'));
        }
    }

    public function lainlain()
    {
        $data = [
            'pageTitle' => 'Owner Input Pendapatan Lain-lain',
            'activeId' => 'pemasukan',
            'activeIdSubMenu' => 'pendapatanll'
        ];

        return view('owner.pemasukan.lain-lain', compact('data'));
    }

    public function pendapatanLainlain(Request $request)
    {
        $url = api_url('admin_pendapatan-lainnya-input.php');

        $data = array(
            '_keterangan' => $request->keterangan,
            '_nominal' => $request->ttl,
            '_metode' => $request->metode,
            '_kodeowner' => Auth::user()->kode,
            '_namaowner' => Auth::user()->nama,
            '_kodekost' => Auth::user()->ownerkost[$this->index]->kode_kost
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
        $data = [
            'pageTitle' => 'Owner Input Pendapatan Lain-lain',
            'activeId' => 'pemasukan',
            'activeIdSubMenu' => 'pendapatanll'
        ];
        // return $result;
        if ($result = 'Auth::user()->ownerkost[$this->index]->kode_kost') {
            Session::flash('message', 'Berhasil melakukan input pendapatan lain-lain');
            return redirect(route('owner.lainlain'));
        } else {
            Session::flash('message', 'Gagal melakukan input pendapatan lain-lain');
            return redirect(route('owner.lainlain'));
        }
    }

    // -------------------------- END OF PEMASUKAN ------------------------------------------//

    // ------------------------- PENGELUARAN -----------------------------//

    public function pengeluaran()
    {
        $data = [
            'pageTitle' => 'Owner Input Pengeluaran',
            'activeId' => 'pengeluaran',
            'activeIdSubMenu' => 'pengeluaran',

        ];
        $url = api_url('admin_pengeluaran-get-jenis.php');
        $json = json_decode(file_get_contents($url), true);

        return view('owner.pengeluaran.index', ['keluar' => $json], compact('data'));
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
        $kodekost = Auth::user()->ownerkost[$this->index]->kode_kost;
        $kodeakun = $request->jenispengeluaran;
        $url = api_url('admin_pengeluaran-input.php');

        $data = array(
            '_keterangan' => $keterangan,
            '_nominal' => $nominal,
            '_metode' => $metode,
            '_kodeakun' => $kodeakun,
            '_kodeowner' => Auth::user()->kode,
            '_namaowner' => Auth::user()->nama,
            '_kodekost' => $kodekost,
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

        if ($result == '{"datapengeluaran":"data pembayaran berhasil diinput"}') {
            Session::flash('message', 'Berhasil melakukan input pengeluaran');
            return redirect(route('owner.pengeluaran'));
        } else {
            Session::flash('message', 'Gagal melakukan input pengeluaran ERROR : ' . $result);
            return redirect(route('owner.pengeluaran'));
        }
    }

    // ---------------------- END OF PENGELUARAN ------------------------- //


    // -------------------------- INFORMASI SEWA ----------------------- //

    public function informasisewa()
    {
        $url = api_url('admin_informasisewa-get-data.php');

        $data = array(
            '_kodekost' => Auth::user()->ownerkost[$this->index]->kode_kost
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
            'pageTitle' => 'Owner Informasi Sewa',
            'activeId' => 'informasisewa',
        ];
        //dd($json);
        return view('owner.informasi_sewa.index', ['info' => $json], compact('data'));
    }

    public function informasisewadetail(Request $request)
    {
        $url = api_url('admin_informasisewa-get-detail.php');

        $data = array(
            '_kodekamar'    => $request->kode,
            '_kodekost'     => Auth::user()->ownerkost[$this->index]->kode_kost
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

        return $result;
    }

    // ----------------------- END OF INFORMASI SEWA --------------------- //

    public function logList()
    {
        $url = api_url('owner_admin-log-getdata.php');
        $data = array(
            '_kodeowner' => Auth::user()->kode,
            '_kodekost' => Auth::user()->ownerkost[$this->index]->kode_kost,
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
            'pageTitle'       => 'Log Admin',
            'activeId'        => 'log',
            'activeIdSubMenu' => ''
        ];

        // dd($json);
        return view('owner.log.list', ['logs' => $json], compact('data'));
    }
}
