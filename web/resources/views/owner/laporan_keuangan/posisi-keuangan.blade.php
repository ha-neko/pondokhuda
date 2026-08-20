@include('layouts.header')
@extends('layouts.sidebar')
@extends('layouts.menubar')
@section('content')

<div class="block-header">
	<h2>{{ $data['pageTitle'] }}</h2>
</div>
@if(Session::has('message'))
<p class="alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ Session::get('message') }}
</p>
@endif

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>
                    Laporan Posisi Keuangan
                </h2>
                <ul class="header-dropdown m-r--5">
                    <li class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons">more_vert</i>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <li><a href="javascript:void(0);" onclick="printData();">Print</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="body table-responsive" id="printReport">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group form-float">
                            <label for="bulan">Pilih Bulan</label>
                            <div class="form-line">
                                <select class="form-control show-tick" name="bulan" id="bulan">
                                    <option value="">-- Pilih Bulan --</option>
                                    @foreach($data['bulans'] as $key => $bln)
                                    @if(($key + 1) == date('m'))
                                    <option value="{{ $key + 1 }}" selected="selected">{{ $bln }}</option>
                                    @else
                                    <option value="{{ $key + 1 }}">{{ $bln }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group form-float">
                            <label for="tahun">Pilih Tahun</label>
                            <div class="form-line">
                                <select class="form-control show-tick" name="tahun" id="tahun">
                                    <option value="">-- Pilih Tahun --</option>
                                    @foreach($data['tahuns'] as $key => $thn)
                                    @if($thn == date('Y'))
                                    <option value="{{ $thn }}" selected="selected">{{ $thn }}</option>
                                    @else
                                    <option value="{{ $thn }}">{{ $thn }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <table width="900px" cellpadding="0" cellspacing="0" id="table-keuangan" class="table">
                    <thead>
                        <tr>
                            <td align="center" colspan="10">
                                <table border="1" width="100%">
                                    <tr>
                                        <td align="center" height="75px" id="judul">
                                            PONDOK HUDA
                                            <br>
                                            LAPORAN POSISI KEUANGAN
                                            <br>
                                            31 JANUARI 20XX
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td width="5%"></td>
                            <td width="45%" valign="top">
                                <table border="0" width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td colspan="4" height="40px">
                                            <u><b>AKTIVA LANCAR :</b></u>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="55%" height="25px">Kas</td>
                                        <td width="5%">Rp</td>
                                        <td width="35%" align="right" id="kas">{{$posisikeus['kas']}}</td>
                                        <td width="5%"></td>
                                    </tr>
                                    <tr>
                                        <td width="55%" height="25px">Bank</td>
                                        <td width="5%">Rp</td>
                                        <td width="35%" align="right" id="bank">{{$posisikeus['bank']}}</td>
                                        <td width="5%"></td>
                                    </tr>
                                    <tr>
                                        <td width="55%" height="25px">Piutang</td>
                                        <td width="5%">Rp</td>
                                        <td width="35%" align="right">-</td>
                                        <td width="5%"></td>
                                    </tr>
                                    <tr>
                                        <td width="55%" height="25px">Sewa Diterima Dimuka</td>
                                        <td width="5%">Rp</td>
                                        <td width="35%" align="right">3,750,000</td>
                                        <td width="5%"></td>
                                    </tr>
                                    <tr>
                                        <td width="55%" height="25px">Pajak Dibayar Dimuka</td>
                                        <td width="5%">Rp</td>
                                        <td width="35%" align="right">-</td>
                                        <td width="5%"></td>
                                    </tr>
                                    <tr>
                                        <td width="55%" height="25px"></td>
                                        <td width="5%">Rp</td>
                                        <td width="35%" align="right">-</td>
                                        <td width="5%"></td>
                                    </tr>
                                    <tr>
                                        <td width="55%" height="25px"></td>
                                        <td width="5%" id="top-line"><b>Rp</b></td>
                                        <td width="35%" align="right" id="top-line"><b>1,337,380</b></td>
                                        <td width="5%"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" height="40px"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" height="40px">
                                            <u><b>AKTIVA TETAP :</b></u>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="55%" height="25px">Tanah dan Bangunan</td>
                                        <td width="5%">Rp</td>
                                        <td width="35%" align="right">-</td>
                                        <td width="5%"></td>
                                    </tr>
                                    <tr>
                                        <td width="55%" height="25px">Kendaraan</td>
                                        <td width="5%">Rp</td>
                                        <td width="35%" align="right">-</td>
                                        <td width="5%"></td>
                                    </tr>
                                    <tr>
                                        <td width="55%" height="25px">Mesin & Perlengkapan</td>
                                        <td width="5%">Rp</td>
                                        <td width="35%" align="right">500,000</td>
                                        <td width="5%"></td>
                                    </tr>
                                    <tr>
                                        <td width="55%" height="25px">Akumulasi Penyusutan</td>
                                        <td width="5%">Rp</td>
                                        <td width="35%" align="right">-</td>
                                        <td width="5%"></td>
                                    </tr>
                                    <tr>
                                        <td width="55%" height="25px"></td>
                                        <td width="5%" id="top-line"><b>Rp</b></td>
                                        <td width="35%" align="right" id="top-line"><b>500,000</b></td>
                                        <td width="5%"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" height="40px"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" height="40px">
                                            <u><b>AKTIVA LAIN :</b></u>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="55%" height="25px">Hak Merk</td>
                                        <td width="5%">Rp</td>
                                        <td width="35%" align="right">-</td>
                                        <td width="5%"></td>
                                    </tr>
                                    <tr>
                                        <td width="55%" height="25px"></td>
                                        <td width="5%" id="top-line"><b>Rp</b></td>
                                        <td width="35%" align="right" id="top-line"><b>-</b></td>
                                        <td width="5%"></td>
                                    </tr>
                                </table>
                            </td>
                            <td width="45%" valign="top">
                                <table border="0" width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td colspan="4" height="40px">
                                            <u><b>UTANG JANGKA PENDEK :</b></u>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="5%"></td>
                                        <td width="55%" height="25px">Utang Usaha</td>
                                        <td width="5%">Rp</td>
                                        <td width="35%" align="right">-</td>
                                    </tr>
                                    <tr>
                                        <td width="5%"></td>
                                        <td width="55%" height="25px">Utang Jk Pendek Lainnya</td>
                                        <td width="5%">Rp</td>
                                        <td width="35%" align="right">70,000</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" height="40px"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" height="40px">
                                            <u><b>UJIAN JANGKA PANJANG :</b></u>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="5%"></td>
                                        <td width="55%" height="25px">Utang Bank</td>
                                        <td width="5%">Rp</td>
                                        <td width="35%" align="right">-</td>
                                    </tr>
                                    <tr>
                                        <td width="5%"></td>
                                        <td width="55%" height="25px">Utang Mobil</td>
                                        <td width="5%">Rp</td>
                                        <td width="35%" align="right">5,122,000</td>
                                    </tr>
                                    <tr>
                                        <td width="5%"></td>
                                        <td width="55%" height="25px"></td>
                                        <td width="5%" id="top-line"><b>Rp</b></td>
                                        <td width="35%" align="right" id="top-line"><b>5,122,000</b></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" height="40px"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" height="40px">
                                            <u><b>MODAL :</b></u>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="5%"></td>
                                        <td width="55%" height="25px">Modal Ditempatkan</td>
                                        <td width="5%">Rp</td>
                                        <td width="35%" align="right">-</td>
                                    </tr>
                                    <tr>
                                        <td width="5%"></td>
                                        <td width="55%" height="25px"></td>
                                        <td width="5%" id="top-line"><b>Rp</b></td>
                                        <td width="35%" align="right" id="top-line"><b>-</b></td>
                                    </tr>
                                </table>
                            </td>
                            <td width="5%"></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="10" height="30px"></td>
                        </tr>
                        <tr>
                            <td width="5%"></td>
                            <td width="45%" valign="top">
                                <table border="0" width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td width="55%" height="25px"></td>
                                        <td width="5%" id="duo-line"><b>Rp</b></td>
                                        <td width="35%" align="right"id="duo-line"><b>1,837,380</b></td>
                                        <td width="5%"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="10" height="1px"></td>
                                    </tr>
                                    <tr>
                                        <td width="55%" height="25px"></td>
                                        <td width="5%" id="top-line"></td>
                                        <td width="35%" align="right"id="top-line"></td>
                                        <td width="5%"></td>
                                    </tr>
                                </table>
                            </td>
                            <td width="45%" valign="top">
                                <table border="0" width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td width="5%"></td>
                                        <td width="55%" height="25px"></td>
                                        <td width="5%" id="duo-line"><b>Rp</b></td>
                                        <td width="35%" align="right" id="duo-line"><b>5,192,000</b></td>
                                    </tr>
                                    <tr>
                                        <td colspan="10" height="1px"></td>
                                    </tr>
                                    <tr>
                                        <td width="5%"></td>
                                        <td width="55%" height="25px"></td>
                                        <td width="5%" id="top-line"></td>
                                        <td width="35%" align="right"id="top-line"></td>
                                    </tr>
                                </table>
                            </td>
                            <td width="5%"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
@section('js-content')
<script type="text/javascript">
    
</script>
@endsection
@include('layouts.footer')