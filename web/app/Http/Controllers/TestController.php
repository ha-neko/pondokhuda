<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function test()
    {
        return view('test');
    }

    public function kostCreate(Request $request)
    {
        $url = api_url('owner_kost-create.php');

        $data = array(
            '_namakost'     => $request->namakost,
            '_alamatkost'   => $request->alamatkost,
            '_emailkost'    => $request->emailkost,
            '_kodeowner'    => Auth::user()->kode,
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

        return redirect()->route('owner.kost');
    }
}
