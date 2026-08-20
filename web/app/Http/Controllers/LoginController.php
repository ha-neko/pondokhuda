<?php

namespace App\Http\Controllers;

use App\Models\Penyewa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Session;
use App\Http\Controllers\OwnerController;

class LoginController extends Controller
{
	public function __construct()
	{
		$this->middleware('guest')->except('logout');
	}

	public function index()
	{
		$data = [
			'pageTitle' => 'Login'
		];

		return view('login', compact('data'));
	}

	public function login(Request $request)
	{
		$url = api_url('login_ph.php');
		$post = array('kode' => $request->kode, 'pin' => $request->pin);
		$options = array(
			'http' => array(
				'header'  => api_header(),
				'method'  => 'POST',
				'content' => http_build_query($post),
				'ignore_errors' => true,
				'timeout' => 15,
			)
		);

		$context  = stream_context_create($options);
		$json = @file_get_contents($url, true, $context);
		if ($json === false) {
			Session::flash('message', 'Server sedang tidak dapat dihubungi. Silakan coba kembali.');
			return redirect()->route('loginForm')->withInput($request->only('kode'));
		}
		$result = json_decode($json, true);
		if (isset($result['error']) && $result['error'] === 'too_many_attempts') {
			$retry = isset($result['retry_after']) ? (int) $result['retry_after'] : 60;
			Session::flash('message', 'Terlalu banyak percobaan login. Coba lagi dalam ' . $retry . ' detik.');
			return redirect()->route('loginForm')->withInput($request->only('kode'));
		}

		// dd($result);

		// print_r($result['personalinfoowner'][0]['id']);

		if (isset($result['personalinfoowner'])) {
			Auth::guard('owner')->LoginUsingId($result['personalinfoowner'][0]['kode']);

			Session::put('index', '0');

			Session::flash('message', 'Anda berhasil login! Selamat datang di menu utama');

			return redirect()->route('owner.dashboard');
		} else if (isset($result['personalinfoadmin'])) {
			Auth::guard('admin')->LoginUsingId($result['personalinfoadmin'][0]['kode']);

			Session::flash('message', 'Anda berhasil login! Selamat datang di menu utama');

			Session::put('index', '0');

			return redirect()->route('admin.dashboard');
		} else if (isset($result['personalinfosuperowner'])) {
			Auth::guard('super-owner')->LoginUsingId($result['personalinfosuperowner'][0]['id']);

			Session::flash('message', 'Anda berhasil login! Selamat datang di menu utama');

			return redirect()->route('super-owner.dashboard');
		} else if (isset($result['personalinfopenyewa'])) {
			Auth::guard('penyewa')->LoginUsingId($result['personalinfopenyewa'][0]['kode']);

			Session::flash('message', 'Anda berhasil login! Selamat datang di menu utama');

			return redirect()->route('penyewa.pembayaran');
		} else {
			Session::flash('message', 'Login gagal! Harap isi username dan password dengan benar');

			return redirect()->route('loginForm');
		}
	}

	public function logout(Request $request)
	{
		if (Auth::guard('owner')->check()) {
			Auth::guard('owner')->logout();
		}
		if (Auth::guard('super-owner')->check()) {
			Auth::guard('super-owner')->logout();
		} else if (Auth::guard('admin')->check()) {
			Auth::guard('admin')->logout();
		} else if (Auth::guard('penyewa')->check()) {
			Auth::guard('penyewa')->logout();
		}

		Session::flush();

		return redirect()->route('loginForm');
	}
}
