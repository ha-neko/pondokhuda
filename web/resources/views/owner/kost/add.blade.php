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

<!-- LIST ADMIN -->
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>
                    Tambah Kost
                </h2>
            </div>
            <div class="body">
                <form onSubmit="return validate()" id="form" action="{{route('owner.kost-create')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <center>
                        <div class="form-group form-float">
                            <div id="imglogo" style="background-image: url('https://pondok-huda.com/Assets/images/logo/default-logo-black.png'); width: 150px; height: 150px; background-size: cover; background-repeat: no-repeat; background-position: 50% 50%; border-radius: 100px; border: 5px solid #d4d4d6;"></div>
                        </div>
                        <input type="file" id="logo" name="logo" onchange="readURL(this);" accept="image/*" style="width:190px"/>
                        <span class="col-red">
                            <label id="logo-info" for="logo" style="font-size:10px">Ukuran Foto Maks 900 KB.</label>
                        </span>
                    </center>
                    <br><br>
                    <div class="form-group form-float">
                        <label class="form-label">Nama Kost</label>
                        <div class="form-line">
                            <input type="text" class="form-control" id="namakost" name="namakost" required>
                        </div>
                    </div>
                    <div class="form-group form-float">
                        <label class="form-label">Alamat Kost</label>
                        <div class="form-line">
                            <input type="text" class="form-control" id="alamatkost" name="alamatkost" required>
                        </div>
                    </div>
                    <div class="form-group form-float">
                        <label class="form-label">Email Kost</label>
                        <div class="form-line">
                            <input type="email" class="form-control" id="emailkost" name="emailkost" required>
                        </div>
                    </div>
                    <div class="form-group form-float">
                        <label class="form-label">Warna Utama</label>
                        <div class="form-group">
                            <input type="hidden" id="primarycolor" name="primarycolor" value="#8BC34A">
                            <input type="hidden" id="namecolor" name="namecolor" value="light-green">
                            <span class="badge bg-light-green" style="cursor: pointer;" onclick="color('#8BC34A', 'light-green')">&nbsp;&nbsp;&nbsp;</span>
                            <span class="badge bg-green" style="cursor: pointer;" onclick="color('#4CAF50', 'green')">&nbsp;&nbsp;&nbsp;</span>
                            <span class="badge bg-red" style="cursor: pointer;" onclick="color('#F44336', 'red')">&nbsp;&nbsp;&nbsp;</span>
                            <span class="badge bg-blue" style="cursor: pointer;" onclick="color('#2196F3', 'blue')">&nbsp;&nbsp;&nbsp;</span>
                            <span class="badge bg-light-blue" style="cursor: pointer;" onclick="color('#03A9F4', 'light-blue')">&nbsp;&nbsp;&nbsp;</span>
                            <span class="badge bg-black" style="cursor: pointer;" onclick="color('#000000', 'black')">&nbsp;&nbsp;&nbsp;</span>
                            <span class="badge bg-purple" style="cursor: pointer;" onclick="color('#9C27B0', 'purple')">&nbsp;&nbsp;&nbsp;</span>
                            <span class="badge bg-brown" style="cursor: pointer;" onclick="color('#795548', 'brown')">&nbsp;&nbsp;&nbsp;</span>
                            <span class="badge bg-grey" style="cursor: pointer;" onclick="color('#9E9E9E', 'grey')">&nbsp;&nbsp;&nbsp;</span>
                            <span class="badge bg-blue-grey" style="cursor: pointer;" onclick="color('#607D8B', 'blue-grey')">&nbsp;&nbsp;&nbsp;</span>
                            <span class="badge bg-pink" style="cursor: pointer;" onclick="color('#E91E63', 'pink')">&nbsp;&nbsp;&nbsp;</span>
                            <span class="badge bg-deep-purple" style="cursor: pointer;" onclick="color('#673AB7', 'deep-purple')">&nbsp;&nbsp;&nbsp;</span>
                            <span class="badge bg-indigo" style="cursor: pointer;" onclick="color('#3F51B5', 'indigo')">&nbsp;&nbsp;&nbsp;</span>
                            <span class="badge bg-cyan" style="cursor: pointer;" onclick="color('#00BCD4', 'cyan')">&nbsp;&nbsp;&nbsp;</span>
                            <span class="badge bg-teal" style="cursor: pointer;" onclick="color('#009688', 'teal')">&nbsp;&nbsp;&nbsp;</span>
                            <span class="badge bg-lime" style="cursor: pointer;" onclick="color('#CDDC39', 'lime')">&nbsp;&nbsp;&nbsp;</span>
                            <span class="badge bg-amber" style="cursor: pointer;" onclick="color('#FFC107', 'amber')">&nbsp;&nbsp;&nbsp;</span>
                            <span class="badge bg-orange" style="cursor: pointer;" onclick="color('#FF9800', 'orange')">&nbsp;&nbsp;&nbsp;</span>
                            <span class="badge bg-deep-orange" style="cursor: pointer;" onclick="color('#FF5722', 'deep-orange')">&nbsp;&nbsp;&nbsp;</span>

                        </div>
                    </div>
                    <div class="text-right">
                        <a href="{{route('owner.kost')}}">
                            <button class="btn waves-effect bg-{{Auth::user()->ownerkost[$indexKost]['kost']['name_color']}}" type="button">
                                <i class="material-icons">keyboard_arrow_left</i>
                                <span class="icon-name">Kembali</span>
                            </button>
                        </a>
                        <button type="submit" onclick="load();" class="btn waves-effect bg-{{Auth::user()->ownerkost[$indexKost]['kost']['name_color']}}">
                            <i class="material-icons">check</i>
                            <span class="icon-name">Tambah Kost</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- END OF LIST ADMIN -->

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

    $(document).ready(function() {
        var $body = $('body');
        var width = $body.width();

        document.getElementById("alamatkost").value.trim();
        if (width < $.AdminBSB.options.leftSideBar.breakpointWidth) {
            document.getElementById("body").className = "theme-" + document.getElementById("namecolor").value + " ls-closed";
        }
        else {
            document.getElementById("body").className = "theme-" + document.getElementById("namecolor").value;
        }

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
    function color(hex, name) {
        var $body = $('body');
        var width = $body.width();

        document.getElementById("primarycolor").value = hex;
        document.getElementById("namecolor").value = name;
        if (width < $.AdminBSB.options.leftSideBar.breakpointWidth) {
            document.getElementById("body").className = "theme-" + name + " ls-closed";
        }
        else {
            document.getElementById("body").className = "theme-" + name;
        }
    }

    function readURL(input) {
        var file = document.getElementById("logo").files[0];
        if(file.size <= 1000000) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    document.getElementById("imglogo").style.backgroundImage = "url('" + e.target.result + "')";
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
        else {
            document.getElementById("logo").value = "";
            document.getElementById("imglogo").style.backgroundImage = "url('https://pondok-huda.com/Assets/images/logo/default-logo-black.png')";
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