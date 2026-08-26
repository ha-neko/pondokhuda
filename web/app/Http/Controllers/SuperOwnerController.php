<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SuperOwnerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:super-owner');
    }

    public function dashboard()
    {
        $url = api_url('so_dashboard.php');
        $result = api_get($url);

        $json = json_decode($result, true);

        $data = [
            'pageTitle'       => 'Super Owner Dashboard',
            'activeId'        => 'dashboard',
            'activeIdSubMenu' => ''
        ];

        return view('super-owner.dashboard', ['report' => $json], compact('data'));
    }
    public function owner()
    {
        $url = api_url('so_owner-get-list.php');
        $result = api_get($url);

        $json = json_decode($result, true);

        $data = [
            'pageTitle'       => 'Owner',
            'activeId'        => 'owner',
            'activeIdSubMenu' => ''
        ];
        return view('super-owner.owner.index', ['owners' => $json], compact('data'));
    }

    public function ownerAdd()
    {
        /*$url = api_url('so_owner-add.php');
        $result = api_get($url);

        $json = json_decode($result, true);*/

        $data = [
            'pageTitle'       => 'Tambah Owner',
            'activeId'        => 'owner',
            'activeIdSubMenu' => ''
        ];
        return view('super-owner.owner.add', /*['kode' => $json],*/ compact('data'));
    }

    public function ownerDetail(Request $request)
    {
        $url = api_url('so_owner-get-detail.php');
        $data = array(
            '_kodeowner' => $request->kode
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

    public function ownerCreate(Request $request)
    {
        $url = api_url('so_owner-create.php');

        if ($request->foto == null) {
            $foto = "0";
        } else {
            $foto = base64_encode(File::get($request->foto));
        }

        $data = array(
            '_kode'      => $request->kode,
            '_nama'      => $request->nama,
            '_notelp'    => $request->notelp,
            '_email'    => $request->email,
            '_foto'      => $foto,
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

        if ($json['owner'] == "owner berhasil ditambah") {
            Session::flash('alert-class', 'alert-success');
            Session::flash('message', 'owner berhasil ditambah');
        } else {
            Session::flash('alert-class', 'alert-danger');
            Session::flash('message', $result);
        }

        return redirect()->route('super-owner.owner');
    }

    public function ownerEdit($id)
    {
        $url = api_url('so_owner-get-detail-edit.php');

        $data = array(
            '_kodeowner' => $id
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
            'pageTitle'       => 'Ubah Data Owner',
            'activeId'        => 'owner',
            'activeIdSubMenu' => ''
        ];

        return view('super-owner.owner.edit', ['owner' => $json], compact('data'));
    }

    public function ownerUpdate(Request $request, $id)
    {
        $url = api_url('so_owner-update.php');

        $data = array(
            '_kode'         => $id,
            '_kode-edit'    => $request->kode,
            '_nama'         => $request->nama,
            '_notelp'       => $request->notelp,
            '_email'        => $request->email,
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

        if ($json['owner'] == "owner berhasil diubah") {
            Session::flash('alert-class', 'alert-success');
            Session::flash('message', "owner berhasil diubah");
        } else {
            Session::flash('alert-class', 'alert-danger');
            Session::flash('message', $result);
        }

        return redirect()->route('super-owner.owner');
    }

    public function ownerDestroy($id)
    {
        $url = api_url('so_owner-delete.php');

        $data = array(
            '_kode' => $id,
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

        if ($json['owner'] == "owner berhasil dihapus") {
            Session::flash('alert-class', 'alert-success');
            Session::flash('message', "owner berhasil dihapus");
        } else {
            Session::flash('alert-class', 'alert-danger');
            Session::flash('message', $result);
        }

        return redirect()->route('super-owner.owner');
    }
}
