@section('css-content')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<link rel="stylesheet" type="text/css" href="https://pondokhuda.com/Assets/custom-loading.css">
<style>
    #kode, #pass {
        background: transparent;
    }
    #btnsub {
        background: #001e4f;
        transform: all 0.3s;
        color: #FFF;
    }
    #btnsub:hover {
        opacity: 0.8;
    }
    #imgBG {
        opacity: 0.1;
        z-index: -1;
    }
    #iseng {
        width: 100%;
        height: 100%;
        position: fixed;
        z-index: -100;
        background: #011330;
        top: 0;
        left: 0;
        transition: all 0.3s;
    }
    .card {
        background: transparent;
        color: #FFF;
    }
    .card input {
        background: transparent;
    }
    #changeColor {
        z-index: 10;
        cursor: pointer;
    }
</style>
@endsection
@include('layouts.header')
<div class="loading" id="loading">Loading&#8230;</div>
<body class="login-page">
    <div id="iseng"></div>
    <img src="https://pondokhuda.com/Assets/images/background-login/bglogin.jpg" id="imgBG">
    <div class="page-loader-wrapper">
        <div class="loader">
            <div class="preloader">
                <div class="spinner-layer pl-red">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
            <p>Mohon tunggu...</p>
        </div>
    </div>
    <div class="login-box">
        <div class="logo">
            <a href="javascript:void(0);">Pondok Huda</a>
        </div>
        <div class="card" id="blogin">
            <div class="body">
                @if(Session::has('message'))
                <p class="alert  alert-danger alert-dismissible"  role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ Session::get('message') }}</p>
                @endif
                <form id="sign_in" onSubmit="load()" action="{{ route('login') }}" method="POST">
                    <div class="msg" style="color: #FFF;">Silahkan login untuk masuk menu utama</div>

                    @csrf
                    
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons" style="color: #FFF;">person</i>
                        </span>
                        <div class="form-line">
                            <input type="text" class="form-control" id="kode" style="color: #FFF; background: transparent;" name="kode" placeholder="Kode" required autofocus>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons" style="color: #FFF;">lock</i>
                        </span>
                        <div class="form-line">
                            <input type="password" id="pass" maxlength="6" minlength="6" style="color: #FFF; background: transparent;" class="form-control" name="pin" placeholder="Pin" required>
                        </div>
                    </div>
                    {{-- <center>
                    <div class="g-recaptcha" data-sitekey="6LddeY8UAAAAAJbC-eaNFBZMbpXAqmj9aNBTh20c"></div>
                    </center> --}}
                    <br>
                    <div class="row">
                        <div class="col-xs-12">
                            <button class="btn btn-block waves-effect" id="btnsub" style="height: 50px;" type="submit">MASUK</button>
                        </div>
                    </div>
                    <div class="row m-t-15 m-b--20">
                        <div class="col-xs-6">
                        </div>
                        <div class="col-xs-6 align-right">
                            <a id="forgotpass" href="javascript:void(0);">Lupa Password?</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <center>
        <span id="changeColor" class="badge bg-deep-orange" style="cursor: pointer;" onclick="color('#FF5722', 'deep-orange', '#ff6b3d')">&nbsp;&nbsp;&nbsp;</span>
        <span id="changeColor" class="badge bg-pink" style="cursor: pointer;" onclick="color('#E91E63', 'pink', '#f94d88')">&nbsp;&nbsp;&nbsp;</span>
        <span id="changeColor" class="badge bg-light-green" style="cursor: pointer;" onclick="color('#8BC34A', 'light-green', '#57990a')">&nbsp;&nbsp;&nbsp;</span>
        <span id="changeColor" class="badge bg-blue" style="cursor: pointer;" onclick="color('#2196F3', 'blue', '#06467a')">&nbsp;&nbsp;&nbsp;</span>
        <span id="changeColor" class="badge bg-black" style="cursor: pointer;" onclick="color('#000000', 'black', '#1b1b1c')">&nbsp;&nbsp;&nbsp;</span>
        <span id="changeColor" class="badge bg-deep-purple" style="cursor: pointer;" onclick="color('#673AB7', 'deep-purple', '#976be5')">&nbsp;&nbsp;&nbsp;</span>

        <br><br>
        <div class="msg" id="tlogin">&copy; Si Juragan Kost 2019</div>
    </center>


    @section('js-content')
    <script type="text/javascript">
        function myFunction(x) {
            if (x.matches) { // If media query matches
                document.getElementById("imgBG").src="https://pondokhuda.com/Assets/images/background-login/bglogin-mobile.jpg";
            } else {
                document.getElementById("imgBG").src="https://pondokhuda.com/Assets/images/background-login/bglogin-desktop.jpg";
            }
        }

        var x = window.matchMedia("(max-width: 750px)")
        myFunction(x) // Call listener function at run time
        x.addListener(myFunction) // Attach listener function on state changes 

        function color(y, d, x) {
            document.getElementById("iseng").style.backgroundColor = y;
            document.getElementById("btnsub").style.background = x;
        }
        function load() {
            if($("#form").valid()) {
                document.getElementById("loading").style.zIndex = "999";
                document.getElementById("loading").style.opacity = "0.8";
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

        $(document).ready(function() {
            $(function () {
                $('#forgotpass').on('click', function () {
                    showSuccessMessage();   
                });
            });

            function showSuccessMessage() {
                swal("Lupa Password?", "Silahkan hubungi owner atau admin!", "info");
            }
        });
    </script>
    @endsection

@include('layouts.footer')