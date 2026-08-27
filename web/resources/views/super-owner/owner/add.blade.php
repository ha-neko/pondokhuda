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
                    Tambah Owner
                </h2>
            </div>
            <div class="body">
                <form onSubmit="return validate()" id="form" action="{{route('super-owner.owner-create')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            {{ $errors->first() }}
                        </div>
                    @endif
                    <center>
                        <div class="form-group form-float">
                            <div id="imgfoto" style="background-image: url(''); width: 150px; height: 150px; background-size: cover; background-repeat: no-repeat; background-position: 50% 50%; border-radius: 100px; border: 5px solid #d4d4d6;"></div>
                            <input type="file" id="foto" name="foto" onchange="readURL(this);" accept="image/*" style="width:190px" />
                            <span class="col-red"><label id="foto-info" for="foto" style="font-size:10px">Ukuran Foto Maks 900 KB.</label></span><br>
                            <b>Dapat di lewati</b>
                        </div>
                    </center>
                    <!-- <div class="form-group form-float">
                        <label class="form-label">Kode Owner</label>
                        <div class="form-line">
                            <input type="text" class="form-control" id="kode" name="kode" readonly="true" value="" required>
                        </div>
                    </div> -->
                    <div class="form-group form-float">
                        <label class="form-label">Nama Owner</label>
                        <div class="form-line">
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                    </div>
                    <div class="form-group form-float">
                        <label class="form-label">Nomor Whatsapp Owner</label>
                        <div class="form-line">
                            <input type="number" onkeypress="numberOnly(event);" class="form-control" id="notelp" name="notelp" required>
                        </div>
                    </div>
                    <div class="form-group form-float">
                        <label class="form-label">Email</label>
                        <div class="form-line">
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn bg-light-green waves-effect" onclick="load()"><i class="material-icons">done</i> <span class="icon-name">Simpan</span></button>
                        <a href="{{route('super-owner.owner')}}">
                            <button class="btn waves-effect bg-light-green" type="button"><i class="material-icons">keyboard_arrow_left</i> <span class="icon-name">Kembali</span></button>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js-content')
<script src="{{ asset('js/validate.js') }}" type="text/javascript"></script>
<script type="text/javascript">

    $(document).ready(function(){
        $('#form').validate({
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
        if(file && file.size <= 921600) {
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
            document.getElementById("imgfoto").style.backgroundImage = "url('')";
            showConfirmMessage();
        }
    }

    function numberOnly(evt) {
      var theEvent = evt || window.event;

      var key = theEvent.keyCode || theEvent.which;
      key = String.fromCharCode(key);
      var regex = /[0-9]|\./;

      if( !regex.test(key) ) {
        theEvent.returnValue = false;
        if(theEvent.preventDefault) theEvent.preventDefault();
      }
    }

</script>

@endsection

@include('layouts.footer')
