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
            @foreach($bio['personalinfopenyewa'] as $biodata)
            <div class="header bg-{{Auth::user()->kost->name_color}}">
                <center>
                    <div style="background-image: url('{{ $biodata['urlfoto'] }}'); width: 150px; height: 150px; background-size: cover; background-repeat: no-repeat; background-position: 50% 50%; border-radius: 100px; border: 5px solid #FFF;"></div>
                    <br>
                    <text style="font-size: 22px;">{{ $biodata['nama'] }}</text>
                    <br><br>
                    <text style="font-size: 15px;">{{ $biodata['tempatkuliahkerja'] }}</text>
                    <br>
                    <text style="font-size: 15px;">{{ $biodata['jurusankuliah'] }}</text>
                </center>
                <ul class="header-dropdown m-r--5">
                    <li class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons">more_vert</i>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <li data-toggle="modal" data-target="#largeModal">
                                <a href="javascript:void(0);">
                                    Ubah Pin
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="body">
                <center>
                <table width="100%">
                    <tr>
                        <td width="50%" valign="top" align="center">
                            <text style="font-size: 240%;">{{ $biodata['nomorkamar'] }}</text>
                            <br>
                            <text style="font-size: 100%; opacity: 0.8;">No. Kamar</text>
                            <br><br>
                            <text style="font-size: 240%;">{{ $biodata['periodebayar'] }}</text>
                            <br>
                            <text style="font-size: 100%; opacity: 0.8;">Periode Bayar<br>(Bulan)</text>
                        </td>
                        <td width="50%" valign="top" style="word-break: break-all;">
                            <text style="font-size: 15px;">{{ $biodata['email'] }}</text>
                            <br>
                            <text style="font-size: 13px; opacity: 0.8;">Email</text>
                            <br><br>
                            <text style="font-size: 15px;">{{ $biodata['nomorhp'] }}</text>
                            <br>
                            <text style="font-size: 13px; opacity: 0.8;">Nomor HP</text>
                            <br><br>
                            <text style="font-size: 15px;">{{ $biodata['alamatrumah'] }}</text>
                            <br>
                            <text style="font-size: 13px; opacity: 0.8;">Alamat Rumah</text>
                        </td>
                    </tr>
                </table>
                </center>
            </div>
            @endforeach
        </div>
    </div>
</div>
<!-- #END# Widgets -->
<div class="modal fade" id="largeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-col-{{Auth::user()->kost->name_color}}">
            <div class="modal-header">
                <h4 class="modal-title" id="largeModalLabel"></h4>
            </div>
            <form id="form" action="{{route('penyewa.ubahpin')}}" method="POST" enctype="multipart/form-data">
                @csrf
            <div class="modal-body">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header">
                                <h2>UBAH PIN</h2>
                            </div>
                            <div class="body">
                                <fieldset>
                                    <div class="form-group form-float">
                                        <label class="form-label">Pin Lama*</label>
                                        <div class="form-line">
                                            <input type="password" id="pinlama" class="form-control" name="pinlama" minlength="6" maxlength="6" required>
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <label class="form-label">Pin Baru*</label>
                                        <div class="form-line">
                                            <input type="password" id="pinbaru" onkeypress="validate(event)" class="form-control" id="pinbaru" minlength="6" maxlength="6" name="pinbaru" required>
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <label class="form-label">Konfirmasi Pin Baru*</label>
                                        <div class="form-line">
                                            <input type="password" id="pinkonfirmasi" onkeypress="validate(event)" class="form-control" id="pinkonfirmasi" minlength="6" maxlength="6" name="pinkonfirmasi" required>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" onclick="load()" class="btn btn-link waves-effect">SAVE</button>
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
            </div>
            </form>
        </div>
    </div>
</div>
<div class="loading" id="loading">Loading&#8230;</div>
@extends('penyewa.whatsapp-admin')

@endsection

@section('js-content')

<script type="text/javascript">
    function load() {
        if (document.getElementById("pinlama").value != "" && document.getElementById("pinbaru").value != "" && document.getElementById("pinkonfirmasi").value != "") {
            document.getElementById("loading").style.zIndex = "9999";
            document.getElementById("loading").style.opacity = "0.8";
        }
    }
    function validate(evt) {
      var theEvent = evt || window.event;

      // Handle paste
      if (theEvent.type === 'paste') {
          key = event.clipboardData.getData('text/plain');
      } else {
      // Handle key press
          var key = theEvent.keyCode || theEvent.which;
          key = String.fromCharCode(key);
      }
      var regex = /[0-9]|\./;
      if( !regex.test(key) ) {
        theEvent.returnValue = false;
        if(theEvent.preventDefault) theEvent.preventDefault();
      }
    }
	$(document).ready(function () {
	    $('#form').validate({
            rules: {
                'pinlama': {
                    pinlama: true
                },
                'pinkonfirmasi': {
                    konfirmasi_pin: true
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

        //Custom Validations ===============================================================================
        //Pin Lama
        $.validator.addMethod('pinlama', function (value, element) {
            return this.optional(element) || value == '{!! Auth::user()->nomorpin !!}';
        },
            'Pin anda tidak sama.'
        );

        //Pin Baru
        $.validator.addMethod('konfirmasi_pin', function (value, element) {
            return this.optional(element) || value == document.getElementById('pinbaru').value;
        },
            'Pin baru tidak sama.'
        );
	});

</script>

@endsection

@include('layouts.footer')