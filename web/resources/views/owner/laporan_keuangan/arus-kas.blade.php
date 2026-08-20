@include('layouts.header')
@extends('layouts.sidebar')
@extends('layouts.menubar')
@section('content')

<div class="block-header">
	<h2>{{ $data['pageTitle'] }}</h2>
</div>

@if(Session::has('message'))
<p class="alert {{ Session::get('alert-class') }} alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ Session::get('message') }}
</p>
@endif

<!-- Basic Examples -->
<div class="row clearfix">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="card">
			<div class="header">
                <h2>
                    Laporan Arus Kas Kost
                </h2>
                <ul class="header-dropdown m-r--5">
                    <li class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons">more_vert</i>
                        </a>
                        <ul class="dropdown-menu pull-right" data-toggle="modal">
                            <li><a href="javascript:void(0);" onclick="printData();">Print Data</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group form-float">
                            <label for="bulan">Pilih Bulan</label>
                            <div class="form-line">
                                <select class="form-control show-tick" name="bulan" id="bulan">
                                    <option value="">-- Pilih Bulan --</option>
                                    @foreach($data['bulans'] as $key=> $bln)
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
                                    @foreach($data['tahuns'] as $key=> $thn)
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
                <div class="table-responsive">
                	<table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                                <th>Debit</th>
                                <th>Kredit</th>
                            </tr>
                        </thead>
                        <tbody id="labaRugi">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js-content')
<script type="text/javascript">

    $(document).on("change", "#bulan", function(){

        var lastday = function(y,m){
            return new Date(y, m +1, 0).getDate();
        }
        var bulan = $(this).val();
        var tahun = $("#tahun").val();

        var tgl   = lastday(tahun, (bulan - 1));
        var bln   = $("#bulan option:selected").text();

        $.ajax({
            type : "post",
            url  : "{{ route('owner.keu_getlabarugi') }}",
            data : { _token: "{{csrf_token()}}", bulan : bulan, tahun : tahun,
                     kodekost : "{{ Auth::user()->kode_kost }}" },
            dataType : "json",
            success :function(data){

                // document.getElementById("").innerHTML = "";

            },
            error : function(data){
                // document.getElementById("").innerHTML = "";
            }
        });
    });

    $(document).on("change", "#tahun", function(){

        var lastday = function(y,m){
            return new Date(y, m +1, 0).getDate();
        }
        var bulan = $("#bulan").val();
        var tahun = $(this).val();

        var tgl   = lastday(tahun, (bulan - 1));
        var bln   = $("#bulan option:selected").text();

        $.ajax({
            type : "post",
            url  : "{{ route('owner.keu_getlabarugi') }}",
            data : { _token: "{{csrf_token()}}", bulan : bulan, tahun : tahun,
                     kodekost : "{{ Auth::user()->kode_kost }}" },
            dataType : "json",
            success :function(data){

                // document.getElementById("").innerHTML = "";

            },
            error : function(data){
                // document.getElementById("").innerHTML = "";
            }
        });
    });

</script>
@endsection

@include('layouts.footer')