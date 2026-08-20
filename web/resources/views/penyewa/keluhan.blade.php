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

<!-- Widgets -->
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
        	<div class="header">
        		<h3>Keluhan Penyewa</h3>
	    		<ul class="header-dropdown m-r--5">
                    <li class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons">more_vert</i>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <li data-toggle="modal" data-target="#largeModal">
                                <a href="javascript:void(0);">
                                    Tambah Keluhan
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
        	</div>
        	@if($keluhan['keluhan'] <= 0 || $keluhan['keluhan'] == null)
        	<div class="header">Tidak ada keluhan</div>
        	@else
        	@foreach($keluhan['keluhan'] as $k)
        	<div class="header" data-toggle="modal" data-target="#komenModal" onclick="showcatmin({!! $k['kode'] !!})">
    			@if(count($k['status']) == 3)
    			<button style="background: #36e833; color: #FFF; border: none; height: 28px">
    				Selesai
    			@elseif(count($k['status']) == 2)
    			<button style="background: #3d3aff; color: #FFF; border: none; height: 28px">
    				Dikerjakan
    			@else
    			<button style="background: #ff1919; color: #FFF; border: none; height: 28px">
    				Pelaporan
    			@endif
	    		</button>
	    		{{ $k['judul'] }}
        	</div>
        	<div class="body" style="border-bottom: 1px solid #48f23c" data-toggle="modal" data-target="#komenModal" onclick="showcatmin({!! $k['kode'] !!})">
        		<p align="justify" style="word-break: normal;">{{ $k['uraian'] }}</p>
                <br><br>
                @foreach($k['status'] as $s)
                <p align="right">{{ $s['status'] }} : {{ $s['tgl'] }}</p>
                @endforeach
        	</div>
        	@endforeach
        	@endif
        </div>
    </div>
</div>
<!-- #END# Widgets -->
<div class="modal fade" id="largeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-col-{{Auth::user()->kost->name_color}}">
            <div class="modal-header">
            </div>
            <form action="{{route('penyewa.keluhanbaru')}}" method="POST" enctype="multipart/form-data">
                @csrf
            <div class="modal-body">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header">
                                <h2>TAMBAH KELUHAN</h2>
                            </div>
                            <div class="body">
                                <fieldset>
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="text" id="judul" class="form-control" name="judul" required>
                                            <label class="form-label">Judul Keluhan*</label>
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <select id="kategori" class="form-control show-tick" name="kategori" required>
                                                <option value="">-- Kategori --</option>
                                                @foreach($keluhan['kategorikeluhan'] as $kategori)
                                                <option value="{{ $kategori['kategori'] }}">{{ $kategori['kategori'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <textarea id="uraian" rows="4" class="form-control no-resize" name="uraian" required></textarea>
                                            <label class="form-label">Uraian*</label>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" onclick="load()" class="btn btn-link waves-effect">SIMPAN</button>
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">TUTUP</button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="komenModal" tabindex="-1" role="dialog">
    <div class="modal-dialog model-lg" role="document">
        <div class="modal-content modal-col-{{Auth::user()->kost->name_color}}">
            <div class="modal-header">
                <span class="right"><button type="button" data-dismiss="modal" class="form-control bg-{{Auth::user()->kost->name_color}} waves-effect" style="border-radius: 0px; border: none; box-shadow: none;"><i class="material-icons">close</i></button></span>
                <h4 class="modal-title" id="largeModalLabel">Catatan Keluhan</h4>
            </div>
            <div class="modal-body">
                <div class="row clearfix">
                    <div class="col-sm-12">
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="hidden" id="kodekeluhan" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div id="note">
                
                </div>
            </div>
        </div>
    </div>
</div>
<div class="loading" id="loading">Loading&#8230;</div>

@extends('penyewa.whatsapp-admin')

@endsection

@section('js-content')

<script type="text/javascript">
function load() {
    if (document.getElementById("judul").value != "" && document.getElementById("kategori").value != "" && document.getElementById("uraian").value != "") {
        document.getElementById("loading").style.zIndex = "9999";
        document.getElementById("loading").style.opacity = "0.8";
    }
}
function showcatmin(kode) {
    document.getElementById("note").innerHTML = "";
    document.getElementById("kodekeluhan").value = kode.toString();
    $.ajax({
        type : "get",
        url  : "{{ route('penyewa.getkomen') }}",
        dataType : "json",
        success :function(data){
            for (var i = 0; i < data['keluhan'].length; i++) {
                if (data['keluhan'][i]['kode'] == kode) {
                    if ( data['keluhan'][i]['komen'].length != 0) {
                        for (var j = 0; j < data['keluhan'][i]['komen'].length; j++) {
                            $("#note").append('<div class="row clearfix"><div class="card"><div class="header" style=" word-break: break-all;">' + data["keluhan"][i]["komen"][j]["komen"] + '</div><div class="body" style="font-size: 13px; color: grey"><span class="right">' + data["keluhan"][i]["komen"][j]["created"] + '</span></div></div>');
                        }
                    }
                    else {
                        $("#note").append("Belum ada catatan");
                    }
                }
            }
        },
        error : function(data){
            $("#note").append("ERROR saat mengambil catatan silahkan periksa koneksi terlebih dahulu atau refresh halaman");
            console.log(data);
        }
    });
}
	$(document).ready(function () {
	    //Widgets count
	    $('.count-to').countTo();
	});

</script>

@endsection

@include('layouts.footer')