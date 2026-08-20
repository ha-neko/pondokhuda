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
                    Ubah Data Owner
                </h2>
            </div>
            <div class="body">
                <form onSubmit="return validate()" id="form" action="{{route('super-owner.owner-update', $owner['owner']['kode'])}}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" name="kode" value="{{ $owner['owner']['kode'] }}">

                    <div class="form-group form-float">
                        <label class="form-label">Nama Owner</label>
                        <div class="form-line">
                            <input type="text" class="form-control" id="nama" name="nama" value="{{ $owner['owner']['nama'] }}" required>
                        </div>
                    </div>
                    <div class="form-group form-float">
                        <label class="form-label">Nomor Whatsapp Owner</label>
                        <div class="form-line">
                            <input type="number" onkeypress="numberOnly(event);" class="form-control" id="notelp" name="notelp" value="{{ $owner['owner']['nomortelepon'] }}" required>
                        </div>
                    </div>
                    <div class="form-group form-float">
                        <label class="form-label">Email</label>
                        <div class="form-line">
                            <input type="email" class="form-control" id="email" name="email" value="{{ $owner['owner']['email'] }}" required>
                        </div>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn bg-light-green waves-effect" onclick="load();"><i class="material-icons">done</i> <span class="icon-name">Simpan</span></button>
                        <a href="{{route('super-owner.owner')}}">
                            <button class="btn waves-effect bg-light-green" type="button"><i class="material-icons">keyboard_arrow_left</i> <span class="icon-name">Kembali</span></button>
                        </a>
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

</script>

@endsection

@include('layouts.footer')