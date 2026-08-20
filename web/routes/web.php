<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Auth::routes();

//Route::get('/home', 'HomeController@index')->name('home');

// Route::get('/test', function () {
// 	return view('test');
// });

Route::group(['middleware' => 'revalidate'], function () {

	Route::get('/', [
		'uses' => 'LoginController@index',
		'as' => 'loginForm'
	]);

	Route::post('/login', [
		'uses' => 'LoginController@login',
		'as' => 'login'
	]);

	Route::post('/logout', [
		'uses' => 'LoginController@logout',
		'as' => 'logout'
	]);


	//---------------- SUPER OWNER -----------------------//


	Route::group(['prefix' => 'super-owner'], function () {

		/*-------------------GET-----------------------*/

		Route::get('/', [
			'uses' => 'SuperOwnerController@dashboard',
			'as' => 'super-owner.dashboard'
		]);
		Route::get('/owner', [
			'uses' => 'SuperOwnerController@owner',
			'as' => 'super-owner.owner'
		]);
		Route::post('/owner/detail/', [
			'uses' => 'SuperOwnerController@ownerDetail',
			'as' => 'super-owner.owner-detail'
		]);
		Route::get('/owner/add', [
			'uses' => 'SuperOwnerController@ownerAdd',
			'as' => 'super-owner.owner-add'
		]);
		Route::post('/owner/create', [
			'uses' => 'SuperOwnerController@ownerCreate',
			'as' => 'super-owner.owner-create'
		]);
		Route::get('/owner/edit/{id}', [
			'uses' => 'SuperOwnerController@ownerEdit',
			'as' => 'super-owner.owner-edit'
		]);
		Route::post('/owner/update/{id}', [
			'uses' => 'SuperOwnerController@ownerUpdate',
			'as' => 'super-owner.owner-update'
		]);
		Route::get('/owner/delete/{id}', [
			'uses' => 'SuperOwnerController@ownerDestroy',
			'as' => 'super-owner.owner-delete'
		]);
	});


	//---------------- OWNER -----------------------//

	Route::group(['prefix' => 'owner'], function () {

		Route::get('/test', [
			'uses' => 'OwnerController@test',
			'as' => 'owner.test'
		]);

		Route::get('/', [
			'uses' => 'OwnerController@dashboard',
			'as' => 'owner.dashboard'
		]);

		Route::get('/first_time_setting', [
			'uses' => 'OwnerController@firstTimeSetting',
			'as' => 'owner.first-time-setting'
		]);

		// ------------- KOST ------------- \\
		Route::get('/kost', [
			'uses' => 'OwnerController@kostList',
			'as' => 'owner.kost'
		]);

		Route::post('/kost/detail', [
			'uses' => 'OwnerController@kostDetail',
			'as' => 'owner.kost-detail'
		]);

		Route::get('/kost/add', [
			'uses' => 'OwnerController@kostAdd',
			'as' => 'owner.kost-add'
		]);

		Route::get('/kost/edit/{id}', [
			'uses' => 'OwnerController@kostEdit',
			'as' => 'owner.kost-edit'
		]);

		Route::post('/kost/create', [
			'uses' => 'OwnerController@kostCreate',
			'as' => 'owner.kost-create'
		]);
		Route::post('/kost/update/{id}', [
			'uses' => 'OwnerController@kostUpdate',
			'as' => 'owner.kost-update'
		]);

		Route::get('/kost/ganti/{index}', [
			'uses' => 'OwnerController@kostGanti',
			'as' => 'owner.kost-ganti'
		]);
		// ------------- END OF KOST ------------- \\

		// ------------- ADMIN ------------- \\
		Route::get('/admin', [
			'uses' => 'OwnerController@adminList',
			'as' => 'owner.admin'
		]);

		Route::post('/admin/detail/', [
			'uses' => 'OwnerController@adminDetail',
			'as' => 'owner.admin-detail'
		]);

		Route::get('/admin/add', [
			'uses' => 'OwnerController@adminAdd',
			'as' => 'owner.admin-add'
		]);

		Route::post('/admin/create', [
			'uses' => 'OwnerController@adminCreate',
			'as' => 'owner.admin-create'
		]);

		Route::get('/admin/edit/{kode}', [
			'uses' => 'OwnerController@adminEdit',
			'as' => 'owner.admin-edit'
		]);

		Route::post('/admin/update', [
			'uses' => 'OwnerController@adminUpdate',
			'as' => 'owner.admin-update'
		]);
		// ------------- END OF ADMIN ------------- \\

		// ------------- PENYEWA ------------- \\
		Route::get('/penyewa', [
			'uses' => 'OwnerController@penyewaList',
			'as' => 'owner.penyewa'
		]);

		Route::post('/penyewa/detail', [
			'uses' => 'OwnerController@penyewaDetail',
			'as' => 'owner.penyewa-detail'
		]);

		Route::post('/penyewa/resendEmailPendaftaran', [
			'uses' => 'OwnerController@penyewaResendEmailDaftar',
			'as' => 'owner.penyewa-resend_email_daftar'
		]);

		Route::post('/penyewa/resendKwitansi', [
			'uses' => 'OwnerController@penyewaResendKwitansi',
			'as' => 'owner.penyewa-resend_kwitansi'
		]);

		Route::get('/penyewa/edit/{id}', [
			'uses' => 'OwnerController@editPenyewa',
			'as' => 'owner.editpenyewa'
		]);
		Route::get('/penyewa/laporan', [
			'uses' => 'OwnerController@penyewaLaporan',
			'as' => 'owner.penyewa-laporan'
		]);
		Route::get('/penyewa/tambah', [
			'uses' => 'OwnerController@tambahPenyewa',
			'as' => 'owner.tambahpenyewa'
		]);
		Route::get('/penyewa/pindah', [
			'uses' => 'OwnerController@pindahPenyewa',
			'as' => 'owner.pindahpenyewa'
		]);
		Route::get('/penyewa/berhenti-sewa', [
			'uses' => 'OwnerController@berhentiSewa',
			'as' => 'owner.berhentisewa'
		]);
		Route::post('/penyewa/berhentisewa', [
			'uses' => 'OwnerController@penyewaBerhentiSewa',
			'as' => 'owner.penyewaberhentisewa'
		]);
		Route::post('/penyewa/update/{id}', [
			'uses' => 'OwnerController@updatePenyewa',
			'as' => 'owner.updatepenyewa'
		]);
		Route::post('/penyewa/createpenyewa', [
			'uses' => 'OwnerController@createPenyewa',
			'as' => 'owner.createpenyewa'
		]);
		Route::post('/penyewa/getkamar', [
			'uses' => 'OwnerController@getKamarPenyewa',
			'as' => 'owner.getkamar'
		]);
		Route::post('/penyewa/hargakamar', [
			'uses' => 'OwnerController@hargaKamar',
			'as' => 'owner.hargakamar'
		]);
		Route::post('/penyewa/pindah-kamar', [
			'uses' => 'OwnerController@pindahKamar',
			'as' => 'owner.pindahkamar'
		]);
		Route::post('/penyewa/get-data-sewa-kamar', [
			'uses' => 'OwnerController@getDataSewaKamar',
			'as' => 'owner.getdatasewakamar'
		]);
		Route::post('/penyewa/get-kamar-pindah', [
			'uses' => 'OwnerController@getDataKamarPindah',
			'as' => 'owner.getkamarpindah'
		]);
		// ------------- END OF PENYEWA ------------- \\

		// -------------- INFORMASI SEWA -------------- \\

		Route::get('/informasi-sewa', [
			'uses' => 'OwnerController@informasiSewa',
			'as' => 'owner.informasisewa'
		]);
		Route::post('/informasi-sewa/detail', [
			'uses' => 'OwnerController@informasisewadetail',
			'as' => 'owner.informasisewa-detail'
		]);

		// ------------- END OF INFORMASI SEWA ----------- \\

		// ------------- KAMAR ------------- \\
		Route::get('/kamar', [
			'uses' => 'OwnerController@kamarList',
			'as' => 'owner.kamar'
		]);

		Route::post('/kamar/detail/', [
			'uses' => 'OwnerController@kamarDetail',
			'as' => 'owner.kamar-detail'
		]);

		Route::get('/kamar/add', [
			'uses' => 'OwnerController@kamarAdd',
			'as' => 'owner.kamar-add'
		]);

		Route::post('/kamar/create', [
			'uses' => 'OwnerController@kamarCreate',
			'as' => 'owner.kamar-create'
		]);

		Route::get('/kamar/edit/{id}', [
			'uses' => 'OwnerController@kamarEdit',
			'as' => 'owner.kamar-edit'
		]);

		Route::post('/kamar/update', [
			'uses' => 'OwnerController@kamarUpdate',
			'as' => 'owner.kamar-update'
		]);
		// ------------- END OF KAMAR ------------- \\


		// ------------- KELUHAN ------------- \\
		Route::get('/keluhan', [
			'uses' => 'OwnerController@keluhan',
			'as' => 'owner.keluhan'
		]);

		Route::get('/keluhan/detail/{id}', [
			'uses' => 'OwnerController@keluhanDetail',
			'as' => 'owner.keluhan-detail'
		]);
		Route::get('/keluhan/get-komen', [
			'uses' => 'OwnerController@getKomenKeluhan',
			'as' => 'owner.komenkeluhan'
		]);
		Route::post('/keluhan/tambah-komen', [
			'uses' => 'OwnerController@tambahKomenKeluhan',
			'as' => 'owner.tambahkomen'
		]);
		Route::get('/keluhan/edit/{id}', [
			'uses' => 'OwnerController@editKeluhan',
			'as' => 'owner.editkeluhan'
		]);
		Route::post('/keluhan/update/{id}', [
			'uses' => 'OwnerController@updateKeluhan',
			'as' => 'owner.updatekeluhan'
		]);
		// ------------- END OF KELUHAN ------------- \\


		// ------------- PENGUMUMAN ------------- \\
		Route::get('/pengumuman', [
			'uses' => 'OwnerController@pengumuman',
			'as' => 'owner.pengumuman'
		]);

		Route::get('/pengumuman/chat', [
			'uses' => 'OwnerController@pengumumanChat',
			'as' => 'owner.pengumumanchat'
		]);
		Route::get('/pengumuman/edit/{id}', [
			'uses' => 'OwnerController@pengumumanEdit',
			'as' => 'owner.editpengumuman'
		]);
		Route::post('/pengumuman/update', [
			'uses' => 'OwnerController@pengumumanUpdate',
			'as' => 'owner.updatepengumuman'
		]);
		Route::post('/pengumuman/create', [
			'uses' => 'OwnerController@pengumumanCreate',
			'as' => 'owner.createpengumuman'
		]);
		// ------------- END OF PENGUMUMAN ------------- \\


		// ------------- LAPORAN KEUANGAN ------------- \\
		// ------------- LAPORAN KEUANGAN TRANSAKSI------------- \\
		Route::get('/keu/transaksi', [
			'uses' => 'OwnerController@keuTransaksi',
			'as' => 'owner.keu_transaksi'
		]);
		// ------------- END OF LAPORAN KEUANGAN TRANSAKSI ------------- \\

		// ------------- LAPORAN KEUANGAN LABA RUGI ------------- \\
		Route::get('/keu/laba-rugi', [
			'uses' => 'OwnerController@keuLabaRugi',
			'as' => 'owner.keu_labarugi'
		]);
		// ------------- END OF LAPORAN KEUANGAN LABA RUGI ------------- \\

		// ------------- LAPORAN KEUANGAN ARUS KAS ------------- \\
		Route::get('/keu/arus-kas', [
			'uses' => 'OwnerController@keuArusKas',
			'as' => 'owner.keu_aruskas'
		]);
		// ------------- END OF LAPORAN KEUANGAN ARUS KAS ------------- \\

		Route::get('/keu/posisi-keuangan', [
			'uses' => 'OwnerController@keuPosisiKeu',
			'as' => 'owner.keu_posisikeu'
		]);
		// ------------- END OF LAPORAN KEUANGAN ------------- \\

		Route::post('/keu/get-transaksi', [
			'uses' => 'OwnerController@getDataTransaksi',
			'as' => 'owner.keu_gettransaksi'
		]);

		Route::post('/keu/get-laba-rugi', [
			'uses' => 'OwnerController@getDataLabaRugi',
			'as' => 'owner.keu_getlabarugi'
		]);

		Route::post('/keu/get-arus-kas', [
			'uses' => 'OwnerController@getDataArusKas',
			'as' => 'owner.keu_getaruskas'
		]);

		// ------------  PEMASUKAN -------------------\\

		Route::get('/pemasukan/pembayaran', [
			'uses' => 'OwnerController@pembayaran',
			'as' => 'owner.pembayaran'
		]);
		Route::post('/pemasukan/bayar', [
			'uses' => 'OwnerController@bayar',
			'as' => 'owner.bayar'
		]);
		Route::post('/pemasukan/getdatasewa', [
			'uses' => 'OwnerController@datasewa',
			'as' => 'owner.getdatasewa'
		]);
		Route::get('/pemasukan/lain-lain', [
			'uses' => 'OwnerController@lainlain',
			'as' => 'owner.lainlain'
		]);
		Route::post('/pemasukan/pendapatan-lain-lain', [
			'uses' => 'OwnerController@pendapatanLainlain',
			'as' => 'owner.pendapatan-lain-lain'
		]);

		// ----------- END OF PEMASUKAN ---------------\\

		// --------------- PENGELUARAN ---------------- \\

		Route::get('/pengeluaran', [
			'uses' => 'OwnerController@pengeluaran',
			'as' => 'owner.pengeluaran'
		]);
		Route::post('/pengeluaran', [
			'uses' => 'OwnerController@inputPengeluaran',
			'as' => 'owner.inputpengeluaran'
		]);

		// ------------END OF PENGELUARAN ----------------\\

		// --------------- LOG ---------------- \\

		Route::get('/log', [
			'uses' => 'OwnerController@logList',
			'as' => 'owner.log'
		]);

		// --------------- END OF LOG ---------------- \\
	});


	//---------------- ADMIN -----------------------//


	Route::prefix('admin')->group(function () {

		//-------------- ADMIN GET ----------------//

		//----------------ADMIN GANTI KOST ------------//
		Route::get('/kost/ganti/{index}', [
			'uses' => 'AdminController@kostGanti',
			'as' => 'admin.kost-ganti'
		]);

		//----------------ADMIN HOME ------------//
		Route::get('/lp', [
			'uses' => 'AdminController@lp',
			'as' => 'admin.lp'
		]);

		Route::get('/', [
			'uses' => 'AdminController@dashboard',
			'as' => 'admin.dashboard'
		]);

		//----------------ADMIN KELUHAN ------------//

		Route::get('/keluhan', [
			'uses' => 'AdminController@keluhan',
			'as' => 'admin.keluhan'
		]);
		Route::get('/keluhan/detailkeluhan/{id}', [
			'uses' => 'AdminController@detailKeluhan',
			'as' => 'admin.detailkeluhan'
		]);
		Route::get('/keluhan/komenkeluhan', [
			'uses' => 'AdminController@getKomenKeluhan',
			'as' => 'admin.komenkeluhan'
		]);

		//----------------ADMIN PENYEWA ------------//

		Route::get('/penyewa', [
			'uses' => 'AdminController@penyewa',
			'as' => 'admin.penyewa'
		]);
		Route::get('/penyewa/create', [
			'uses' => 'AdminController@viewCreatePenyewa',
			'as' => 'admin.createpenyewaview'
		]);
		Route::get('/penyewa/edit/{id}', [
			'uses' => 'AdminController@viewEditPenyewa',
			'as' => 'admin.editpenyewa'
		]);
		Route::get('/penyewa/pindah-kamar', [
			'uses' => 'AdminController@penyewaPindahKamar',
			'as' => 'admin.pindahkamar'
		]);
		Route::get('/penyewa/stop', [
			'uses' => 'AdminController@penyewaStopSewa',
			'as' => 'admin.berhentisewa'
		]);

		//----------------ADMIN PENGUMUMAN ------------//

		Route::get('/pengumuman', [
			'uses' => 'AdminController@pengumuman',
			'as' => 'admin.pengumuman'
		]);
		Route::get('/pengumuman/detail/{id}', [
			'uses' => 'AdminController@detailPengumuman',
			'as' => 'admin.detailpengumuman'
		]);

		//----------------ADMIN PEMASUKAN ------------//

		Route::get('/pembayaran', [
			'uses' => 'AdminController@pembayaran',
			'as' => 'admin.pembayaran'
		]);
		Route::get("/pendapatan-lain-lain", [
			'uses' => 'AdminController@viewPendapatanLL',
			'as' => 'admin.viewpendpatanll'
		]);

		//----------------ADMIN PENGELUARAN ------------//

		Route::get('/pengeluaran', [
			'uses' => 'AdminController@viewPengeluaran',
			'as' => 'admin.pengeluaran'
		]);

		Route::get('/informasi-sewa', [
			'uses' => 'AdminController@informasisewa',
			'as' => 'admin.informasisewa'
		]);

		Route::post('/informasi-sewa/detail', [
			'uses' => 'AdminController@informasisewadetail',
			'as' => 'admin.informasisewa-detail'
		]);

		//-------------- ADMIN POST ----------------//

		//----------------ADMIN KELUHAN ------------//

		Route::post('/keluhan/update/{id}', [
			'uses' => 'AdminController@updateKeluhan',
			'as' => 'admin.updatekeluhan'
		]);
		Route::post('/keluhan/update', [
			'uses' => 'AdminController@updateOff',
			'as' => 'admin.updateOff'
		]);
		Route::post('/keluhan/createnote', [
			'uses' => 'AdminController@tambahKomenKeluhan',
			'as' => 'admin.createkomenkeluhan'
		]);

		//----------------ADMIN PENYEWA ------------//

		Route::post('/penyewa/create', [
			'uses' => 'AdminController@createPenyewa',
			'as' => 'admin.createpenyewa'
		]);
		Route::post('/penyewa/resendEmailPendaftaran', [
			'uses' => 'AdminController@penyewaResendEmailDaftar',
			'as' => 'admin.penyewa-resend_email_daftar'
		]);

		Route::post('/penyewa/resendKwitansi', [
			'uses' => 'AdminController@penyewaResendKwitansi',
			'as' => 'admin.penyewa-resend_kwitansi'
		]);
		Route::post('/penyewa/create/getkamar', [
			'uses' => 'AdminController@getDataKamarCreate',
			'as' => 'admin.getkamarcreate'
		]);
		Route::post('/getkotakab', [
			'uses' => 'AdminController@getkotakab',
			'as' => 'admin.kotakab'
		]);
		Route::post('/getkecamatan', [
			'uses' => 'AdminController@getkecamatan',
			'as' => 'admin.kecamatan'
		]);
		Route::post('/getkelurahan', [
			'uses' => 'AdminController@getkelurahan',
			'as' => 'admin.kelurahan'
		]);
		Route::post('/getkodepos', [
			'uses' => 'AdminController@getkodepos',
			'as' => 'admin.kodepos'
		]);
		Route::post('/penyewa/update/{id}', [
			'uses' => 'AdminController@updatePenyewa',
			'as' => 'admin.updatepenyewa'
		]);
		Route::post('/hargakamar', [
			'uses' => 'AdminController@hargakamar',
			'as' => 'admin.hargakamar'
		]);
		Route::post('/penyewa/getdatasewa', [
			'uses' => 'AdminController@getDataSewaKamar',
			'as' => 'admin.getdatasewakamar'
		]);

		Route::post('/penyewa/pindahkamar', [
			'uses' => 'AdminController@pindahKamar',
			'as' => 'admin.pindahkamarbaru'
		]);
		Route::post('/penyewa/pindahkamar/getdatakamarpindah', [
			'uses' => 'AdminController@getDataKamarPindah',
			'as' => 'admin.getkamarpindah'
		]);
		Route::post('/penyewa/stop-sewa', [
			'uses' => 'AdminController@stopSewa',
			'as' => 'admin.stopsewa'
		]);

		//----------------ADMIN PENGUMUMAN ------------//

		Route::post('/pengumuman/create', [
			'uses' => 'AdminController@createPengumuman',
			'as' => 'admin.createpengumuman'
		]);
		Route::post('/pengumuman/edit', [
			'uses' => 'AdminController@editPengumuman',
			'as' => 'admin.editpengumuman'
		]);

		//----------------ADMIN PEMBAYARAN ------------//

		Route::post('/pembayaran/datasewa', [
			'uses' => 'AdminController@datasewa',
			'as' => 'admin.getdatasewa'
		]);
		Route::post('/pembayaran/bayar', [
			'uses' => 'AdminController@inputBayar',
			'as' => 'admin.bayar'
		]);
		Route::post('/pembayaran/lain-lain', [
			'uses' => 'AdminController@pendapatanLainlain',
			'as' => 'admin.pendapatanlainlain'
		]);

		//----------------ADMIN PENGELUARAN ------------//

		Route::post('/pengeluaran/input', [
			'uses' => 'AdminController@inputPengeluaran',
			'as' => 'admin.inputpengeluaran'
		]);
	});


	//---------------- PENYEWA -----------------------//


	Route::prefix('penyewa')->group(function () {
		/*-----------------GET-------------------*/
		Route::get('/', [
			'uses' => 'PenyewaController@dashboard',
			'as' => 'penyewa.dashboard'
		]);
		Route::get('/pembayaran', [
			'uses' => 'PenyewaController@pembayaran',
			'as' => 'penyewa.pembayaran'
		]);
		Route::get('/pengumuman', [
			'uses' => 'PenyewaController@pengumuman',
			'as' => 'penyewa.pengumuman'
		]);
		Route::get('/keluhan', [
			'uses' => 'PenyewaController@keluhan',
			'as' => 'penyewa.keluhan'
		]);
		Route::get('/riwayat-pembayaran', [
			'uses' => 'PenyewaController@riwayatpembayaran',
			'as' => 'penyewa.riwayatpembayaran'
		]);
		Route::get('/komekeluhan', [
			'uses' => 'PenyewaController@getKomenKeluhan',
			'as' => 'penyewa.getkomen'
		]);

		/*-----------------POST-------------------*/
		Route::post('/keluhanbaru', [
			'uses' => 'PenyewaController@keluhanbaru',
			'as' => 'penyewa.keluhanbaru'
		]);
		Route::post('/ubahpin', [
			'uses' => 'PenyewaController@ubahpin',
			'as' => 'penyewa.ubahpin'
		]);
	});
});
