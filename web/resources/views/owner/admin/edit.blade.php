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
                    Edit Data Admin
                </h2>
            </div>
            <div class="body">
            	<form onSubmit="return validate()" id="form" action="{{route('owner.admin-update')}}" method="POST" enctype="multipart/form-data">
	                @csrf
                    <center>
                        <div class="form-group form-float">
                            <div id="imgfoto" style="background-image: url('{{$admin['admin'][0]['urlfoto']}}'); width: 150px; height: 150px; background-size: cover; background-repeat: no-repeat; background-position: 50% 50%; border-radius: 100px; border: 5px solid #d4d4d6;"></div>
                            <input type="file" value="" id="foto" name="foto" onchange="readURL(this);" accept="image/*" style="width:190px"/>
                            <span class="col-red">
                                <label id="foto-info" for="foto" style="font-size:10px">Ukuran Foto Maks 900 KB.</label>
                            </span>
                        </div>
                    </center>
                    <div class="form-group form-float">
                        <label class="form-label">Kode Admin</label>
                        <div class="form-line">
                            <input type="text" value="{{$admin['admin'][0]['kode']}}" class="form-control" id="kode" name="kode" readonly="true">
                        </div>
                    </div>
                    <div class="form-group form-float">
                        <label class="form-label">Nama Admin</label>
                        <div class="form-line">
                            <input type="text" value="{{$admin['admin'][0]['nama']}}" class="form-control" id="nama" name="nama" required>
                        </div>
                    </div>
                    <div class="form-group form-float">
                        <label class="form-label">Pin Admin</label>
                        <div class="form-line">
                            <input type="number" value="{{$admin['admin'][0]['pin']}}" class="form-control" id="pin" name="pin" required>
                        </div>
                    </div>
                    <div class="form-group form-float">
                        <label class="form-label">Nomor Telepon Admin</label>
                        <div class="form-line">
                            <input type="number" value="{{$admin['admin'][0]['nomortelepon']}}" class="form-control" id="notelp" name="notelp" required>
                        </div>
                    </div>
                    <div class="form-group form-float">
                        <label for="kost" id="labelkost">Pilih Penempatan Kost</label>
                        <div class="demo-checkbox required" id="checkkost">
                            @php
                            $index=0;
                            @endphp
                            @if(count(Auth::user()->ownerkost) != 0)
                                @foreach(Auth::user()->ownerkost as $key => $kost)
                                    <input type="checkbox" name="kost[]" id="{{$kost['kost']['kode_kost']}}" 
                                    value="{{$kost['kost']['kode_kost']}}"/>
                                    <label for="{{$kost['kost']['kode_kost']}}">{{$kost['kost']['nama_kost']}}</label>
                                    <br>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    @foreach($admin['admin'] as $a)
                    <input type="hidden" name="listkost[]" value="{{$a['kodekost']}}">
                    @endforeach
                    <div class="text-right">
                        <a href="{{ route('owner.admin') }}">
                            <button class="btn waves-effect bg-{{Auth::user()->ownerkost[$indexKost]['kost']['name_color']}}" type="button">
                                <i class="material-icons">keyboard_arrow_left</i>
                                <span class="icon-name">Kembali</span>
                            </button>
                        </a>
                        <button type="submit" onclick="load();" class="btn waves-effect bg-{{Auth::user()->ownerkost[$indexKost]['kost']['name_color']}}">
                            <i class="material-icons">check</i>
                            <span class="icon-name">Udah Data Admin</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="loading" id="loading">Loading&#8230;</div>
@endsection

@section('js-content')
<script src="https://pondok-huda.com/js/validate.js" type="text/javascript"></script>
<script type="text/javascript">

    function load() {
        if ($("#form").valid()) {
            document.getElementById("loading").style.zIndex = "999";
            document.getElementById("loading").style.opacity = "0.8";
            showInputMessage();
        }
    }

    $(document).ready(function(){
        for(i = 0; i < document.getElementsByName("listkost[]").length; i++)
        {
            $("input[type=checkbox][value='"+document.getElementsByName("listkost[]")[i].value+"']").prop('checked',true);
        }

        $('#form').validate({
            rules: {
                'kost[]': {
                    required: true
                }
            },
            highlight: function (input) {
                $(input).parents('.form-line').addClass('error');
            },
            unhighlight: function (input) {
                $(input).parents('.form-line').removeClass('error');
            },
            errorPlacement: function (error, element) {
                $(element).parents('.form-group').append(error);
            }
        });

    });

    function readURL(input) {
        var file = document.getElementById("foto").files[0];
        if(file.size <= 1000000) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    document.getElementById("imgfoto").style.backgroundImage = "url('" + e.target.result + "')";
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
        else {
            document.getElementById("foto").value = "";
            document.getElementById("imgfoto").style.backgroundImage = "url('{{$admin['admin'][0]['urlfoto']}}')";
            showConfirmMessage();
        }
    }
    function showConfirmMessage() {
        swal({
            title: "Ukuran File Terlalu Besar",
            text: "Mohon untuk memilih file dengan ukuran dibawah 900 KB",
            type: "warning",
            closeOnConfirm: true
        });
    }

</script>

@endsection

@include('layouts.footer')