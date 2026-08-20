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
        <div class="body">
            <fieldset>
            	@if($penyewa['datapenyewa'] != null)
            	@foreach($penyewa['datapenyewa'] as $p)
            	@if($p['kode'] == $kode)
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="body">
        						<form onSubmit="return validate()" id="form" action="{{ route('admin.updatepenyewa', $kode) }}" method="post" enctype="multipart/form-data">
        						@csrf
        						<center>
        						<div id="img-foto" style="background-image: url('https://pondok-huda.com{{ $p['urlfoto'] }}'); width: 150px; height: 150px; background-size: cover; background-repeat: no-repeat; background-position: 50% 50%; border-radius: 100px; border: 5px solid #d4d4d6;"></div>

                                    <input type="file" name="foto" onchange="readURL(this);" accept="image/*" />
        						</center>
                                <h3>Informasi Umum</h3>
                                <fieldset>
                                    <div class="form-group form-float">
                                        <label class="form-label">No. KTP</label>
                                        <div class="form-line disabled">
                                            <input type="text" id="noktp" class="form-control disabled" name="noktp" placeholder="Masukkan nomor KTP" value="{{ $p['noktp'] }}" disabled="" readonly="true">
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <label class="form-label">Nama</label>
                                        <div class="form-line">
                                            <input type="text" id="nama" class="form-control" name="nama" placeholder="Masukkan nama" value="{{ $p['nama'] }}" required>
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <label class="form-label">Tanggal lahir</label>
                                        <div class="form-line" >
                                            <input type="text" id='datePicker' class="form-control" name="tgllhr" placeholder="Masukkan tanggal lahir" value="{{ $p['tgllahir'] }}" required>
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <label class="form-label">No. HP</label>
                                        <div class="form-line">
                                            <input type="text" id="nohp" class="form-control" name="nohp" placeholder="Masukkan nomor HP" value="{{ $p['hp'] }}" required>
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <label for="me">Jenis Kelamin</label>
                                        <div class="form-group">
                                        	@if($p['jk'] == 'Pria')
                                        	<input type="radio" name="gender" id="male" class="with-gap" value="Pria" checked>
                                            <label for="male">Laki - laki</label>
                                            <input type="radio" name="gender" id="female" class="with-gap" value="Wanita">
                                            <label for="female" class="m-l-20">Perempuan</label>
                                            @else
                                            <input type="radio" name="gender" id="male" class="with-gap" value="Pria">
                                            <label for="male">Laki - laki</label>
                                            <input type="radio" name="gender" id="female" class="with-gap" value="Wanita" checked>
                                            <label for="female" class="m-l-20">Perempuan</label>
                                        	@endif
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <label class="form-label">Email</label>
                                        <div class="form-line">
                                            <input type="email" id="email" class="form-control" name="email" placeholder="Masukkan email" value="{{ $p['email'] }}" required>
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <label class="form-label">Nama Orang tua/Wali</label>
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="namaortu" placeholder="Masukkan nama Orang Tua/Wali" value="{{ $p['namaortu'] }}" >
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <label class="form-label">No. HP Orang tua</label>
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="nohportu" placeholder="Masukkan nomor HP Orang Tua" value="{{ $p['hportu'] }}" >
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <label class="form-label">Lokasi Kuliah/Kerja</label>
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="tempatkuliahkerja" value="{{ $p['tempatkuliahkerja'] }}" placeholder="Masukkan lokasi kuliah/kerja" >
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <label class="form-label">Jurusan Kuliah</label>
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="jurkuliah" placeholder="Masukkan jurusan kuliah" value="{{ $p['jurusankul'] }}" >
                                        </div>
                                    </div>
                                </fieldset>

                                <h3>Informasi Domisili</h3>
                                <fieldset>
                                    <div class="form-group form-float">
                                        <label class="form-label">Alamat kirim surat</label>
                                        <div class="form-line">
                                            <input type="text" name="alamat" class="form-control" placeholder="Masukkan alamat" value="{{ $p['alamat'] }}" >
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <label for="provinsi">Provinsi</label>
                                        <text id="textD" style="opacity: 0.5;">&nbsp;{{ $p['provinsi'] }}</text>
                                        <div class="form-line">
                                            <select class="form-control show-tick" style="display: none;" name="provinsi" id="cboxProv">
                                                <option value="">-- Pilih Provinsi --</option>
                                                @foreach($provinsi['getdataprov'] as $k)
                                                <option value="{{ $k['provinsi'] }}">{{ $k['provinsi'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <label for="kotakab">Kota/Kabupaten</label>
                                        <text id="textD" style="opacity: 0.5;">&nbsp;{{ $p['kotakab'] }}</text>
                                        <div class="form-line">
                                            <select class="form-control show-tick" style="display: none;" name="kotakab" id="cboxKotakab" disabled="">
                                                <option value="">-- Pilih Kota/Kabupaten --</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <label for="kecamatan">Kecamatan</label>
                                        <text id="textD" style="opacity: 0.5;">&nbsp;{{ $p['kecamatan'] }}</text>
                                        <div class="form-line">
                                            <select class="form-control show-tick" style="display: none;" name="kecamatan" id="cboxKecamatan" disabled="">
                                                <option value="">-- Pilih Kecamatan --</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <label for="kelurahan">Kelurahan</label>
                                        <text id="textD" style="opacity: 0.5;">&nbsp;{{ $p['kelurahan'] }}</text>
                                        <div class="form-line">
                                            <select class="form-control show-tick" style="display: none;" name="kelurahan" id="cboxKelurahan" disabled="">
                                                <option value="">-- Pilih Kelurahan --</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <label for="kodepos">Kode Pos</label>
                                        <div class="form-line" style="opacity: 0.5;">
                                            <input type="text" name="kodepos" class="form-control" id="kodepos"  readonly="true" value="{{ $p['kodepos'] }}" placeholder="Kode Pos" >
                                        </div>
                                    </div>
                                    <a onclick="editDomisili()">
        								<button class="btn bg-{{Auth::user()->adminkost[$indexKost]['kost']['name_color']}} waves-effect" id="editDomisili" type="button"><i id="iconbtn" class="material-icons">mode_edit</i> <span id="btned" class="icon-name">Edit Domisili</span></button>
        								<input type="hidden" name="ed" id="ed" value="off">
        							</a>
        							<br><br>
                                    <div class="form-group form-float">
                                        <label for="status">Status</label>
                                        <div class="form-line">
                                            @if($p['status'] == 'on')
                                        	<input type="radio" name="status" id="on" class="with-gap" value="on" checked>
                                            <label for="on">On</label>
                                            <input type="radio" name="status" id="off" class="with-gap" value="off">
                                            <label for="off" class="m-l-20">Off</label>
                                            @else
                                            <input type="radio" name="status" id="on" class="with-gap" value="on">
                                            <label for="on">On</label>
                                            <input type="radio" name="status" id="off" class="with-gap" value="off" checked>
                                            <label for="off" class="m-l-20">Off</label>
                                        	@endif
                                        </div>
                                    </div>
                                </fieldset>
                                <div class="text-right">
	                                <a href="{{ route('admin.penyewa') }}">
							        <button class="btn waves-effect bg-{{Auth::user()->adminkost[$indexKost]['kost']['name_color']}}" type="button"><i class="material-icons">keyboard_arrow_left</i> <span class="icon-name">Kembali</span></button>
							        </a>
	                                <button class="btn bg-{{Auth::user()->adminkost[$indexKost]['kost']['name_color']}} waves-effect" type="submit" onclick="load()"><i class="material-icons">file_upload</i> <span class="icon-name">Update</span></button>
                            	</div>
        						</form>
                            </div>
                        </div>
                    </div>
                </div>
            	@endif
            	@endforeach
            	@else
            	<h3 style="color: #FFF">Tidak ada Data penyewa</h3>
            	@endif
            </fieldset>
        </div>
    </div>
</div>
<div class="loading" id="loading">Loading&#8230;</div>
@endsection
@section('js-content')
<script src="https://pondok-huda.com/js/validate.js" type="text/javascript"></script>
<script type="text/javascript">
function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            // $('#img-foto').attr('src', e.target.result);
            // alert(e.target.result);
            document.getElementById("img-foto").style.backgroundImage = "url('" + e.target.result + "')";
        }

        reader.readAsDataURL(input.files[0]);
    }
    alert(e.target.result);
}
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
var c = false;
var kpv;
var posd;
// window.onload = function() {
//     document.getElementById("cboxProv").selectedIndex = 0;
// }
// function load() {
//     if (c == false) {
//         if (document.getElementById("noktp").value != "" && document.getElementById("nama").value != "" && document.getElementById("datePicker").value != "" && document.getElementById("nohp").value != "" && document.getElementById("email").value != "") {
//             document.getElementById("loading").style.zIndex = "999";
//             document.getElementById("loading").style.opacity = "0.8";
//         }
//     }
//     else {
//         if (document.getElementById("noktp").value != "" && document.getElementById("nama").value != "" && document.getElementById("datePicker").value != "" && document.getElementById("nohp").value != "" && document.getElementById("email").value != "" && document.getElementById("cboxProv").value != "" && document.getElementById("cboxKotakab").value != "" && document.getElementById("cboxKecamatan").value != "" && document.getElementById("cboxKelurahan").value != "") {
//             document.getElementById("loading").style.zIndex = "999";
//             document.getElementById("loading").style.opacity = "0.8";
//         }
//     }
// }
function load() {
    if ($("#form").valid()) {
        document.getElementById("loading").style.zIndex = "999";
        document.getElementById("loading").style.opacity = "0.8";
        showInputMessage(); 
    }
}
function editDomisili() {
	if (c == false) {
		document.getElementById("cboxProv").style.display = "block";
		document.getElementById("cboxKotakab").style.display = "block";
		document.getElementById("cboxKecamatan").style.display = "block";
		document.getElementById("cboxKelurahan").style.display = "block";
		document.getElementById("editDomisili").className = "btn bg-red waves-effect";
		kpv = document.getElementById("kodepos").value;
		if (posd == null) {
			document.getElementById("kodepos").value = "";
		}
		else {
			document.getElementById("kodepos").value = posd;
		}

		document.getElementById("cboxProv").setAttribute("required", "true");
		document.getElementById("cboxKotakab").setAttribute("required", "true");
		document.getElementById("cboxKecamatan").setAttribute("required", "true");
		document.getElementById("cboxKelurahan").setAttribute("required", "true");
		document.getElementById("ed").value = "on";
		c = true;
		document.getElementById("btned").innerHTML = "Batal";
		document.getElementById("iconbtn").innerHTML = "cancel";
	}
	else {
		document.getElementById("cboxProv").style.display = "none";
		document.getElementById("cboxKotakab").style.display = "none";
		document.getElementById("cboxKecamatan").style.display = "none";
		document.getElementById("cboxKelurahan").style.display = "none";
		document.getElementById("editDomisili").className = "btn bg-{{Auth::user()->adminkost[$indexKost]['kost']['name_color']}} waves-effect";
		document.getElementById("kodepos").value = kpv;

		document.getElementById("cboxProv").removeAttribute("required");
		document.getElementById("cboxKotakab").removeAttribute("required");
		document.getElementById("cboxKecamatan").removeAttribute("required");
		document.getElementById("cboxKelurahan").removeAttribute("required");
        document.getElementById("cboxProv").value = "";
        document.getElementById("cboxKotakab").value = "";
        document.getElementById("cboxKecamatan").value = "";
        document.getElementById("cboxKelurahan").value = "";
		document.getElementById("ed").value = "off";
		c = false;
		document.getElementById("btned").innerHTML = "Edit Domisili";
		document.getElementById("iconbtn").innerHTML = "mode_edit";
	}
}
$(document).on("change","#cboxProv",function(){
    document.getElementById("loading").style.zIndex = "9999";
    document.getElementById("loading").style.opacity = "0.8";
    var nilai = $(this).val();
    $.ajax({
        type : "post",
        url  : "{{ route('admin.kotakab') }}",
        data : { _token: "{{csrf_token()}}", nilai : nilai},
        dataType : "json",
        success :function(data){
            var i;
            var x = document.getElementById("cboxKotakab");
            var length = x.options.length;
            for (i = length; i > 0; i--) {
                x.options[i] = null;
            }
            var x = document.getElementById("cboxKecamatan");
            var length = x.options.length;
            for (i = length; i > 0; i--) {
                x.options[i] = null;
            }
            document.getElementById("cboxKecamatan").disabled = true;
            var x = document.getElementById("cboxKelurahan");
            var length = x.options.length;
            for (i = length; i > 0; i--) {
                x.options[i] = null;
            }
            document.getElementById("cboxKelurahan").disabled = true;
            document.getElementById("kodepos").value = "";
            for (i = 0; i < data['getdatakotakab'].length; i++) {
                document.getElementById("cboxKotakab").disabled = false;
                var x = document.getElementById("cboxKotakab");
                var option = document.createElement("option");
                option.text = data['getdatakotakab'][i]['kotakab'];
                option.value = data['getdatakotakab'][i]['kotakab'];
                x.add(option);
            }
            posd = null;
            document.getElementById("loading").style.zIndex = "-9999";
            document.getElementById("loading").style.opacity = "0";
        },
        error : function(data){
            document.getElementById("cboxKotakab").disabled = true;
            document.getElementById("cboxKecamatan").disabled = true;
            document.getElementById("cboxKelurahan").disabled = true;
            var x = document.getElementById("cboxKotakab");
            var length = x.options.length;
            for (i = length; i > 0; i--) {
                x.options[i] = null;
            }
            document.getElementById("loading").style.zIndex = "-9999";
            document.getElementById("loading").style.opacity = "0";
        }
    });
});

$(document).on("change","#cboxKotakab",function(){
    document.getElementById("loading").style.zIndex = "9999";
    document.getElementById("loading").style.opacity = "0.8";
    var nilai1 = $("#cboxProv").val();
    var nilai2 = $(this).val();
    
    $.ajax({
        type : "post",
        url  : "{{ route('admin.kecamatan') }}",
        data : { _token: "{{csrf_token()}}", nilai1 : nilai1, nilai2 : nilai2},
        dataType : "json",
        success :function(data){
            var i;
            var x = document.getElementById("cboxKecamatan");
            var length = x.options.length;
            for (i = length; i > 0; i--) {
                x.options[i] = null;
            }
            var x = document.getElementById("cboxKelurahan");
            var length = x.options.length;
            for (i = length; i > 0; i--) {
                x.options[i] = null;
            }
            document.getElementById("cboxKelurahan").disabled = true;
            document.getElementById("kodepos").value = "";
            for (i = 0; i < data['getdatakecamatan'].length; i++) {
                document.getElementById("cboxKecamatan").disabled = false;
                var x = document.getElementById("cboxKecamatan");
                var option = document.createElement("option");
                option.text = data['getdatakecamatan'][i]['kecamatan'];
                option.value = data['getdatakecamatan'][i]['kecamatan'];
                x.add(option);
            }
            posd = null;
            document.getElementById("loading").style.zIndex = "-9999";
            document.getElementById("loading").style.opacity = "0";
        },
        error : function(data){
            document.getElementById("cboxKecamatan").disabled = true;
            document.getElementById("cboxKelurahan").disabled = true;
            var x = document.getElementById("cboxKecamatan");
            var length = x.options.length;
            for (i = length; i > 0; i--) {
                x.options[i] = null;
            }
            document.getElementById("loading").style.zIndex = "-9999";
            document.getElementById("loading").style.opacity = "0";
        }
    });
});

$(document).on("change","#cboxKecamatan",function(){
    document.getElementById("loading").style.zIndex = "9999";
    document.getElementById("loading").style.opacity = "0.8";
    var nilai1 = $("#cboxProv").val();
    var nilai2 = $("#cboxKotakab").val();
    var nilai3 = $(this).val();
    
    $.ajax({
        type : "post",
        url  : "{{ route('admin.kelurahan') }}",
        data : { _token: "{{csrf_token()}}", nilai1 : nilai1, nilai2 : nilai2, nilai3 : nilai3},
        dataType : "json",
        success :function(data){
            var i;
            var x = document.getElementById("cboxKelurahan");
            var length = x.options.length;
            for (i = length; i > 0; i--) {
                x.options[i] = null;
            }
            document.getElementById("kodepos").value = "";
            for (i = 0; i < data['getdatakelurahan'].length; i++) {
                document.getElementById("cboxKelurahan").disabled = false;
                var x = document.getElementById("cboxKelurahan");
                var option = document.createElement("option");
                option.text = data['getdatakelurahan'][i]['kelurahan'];
                option.value = data['getdatakelurahan'][i]['kelurahan'];
                x.add(option);
            }
            posd = null;
            document.getElementById("loading").style.zIndex = "-9999";
            document.getElementById("loading").style.opacity = "0";
        },
        error : function(data){
            document.getElementById("cboxKelurahan").disabled = true;
            var x = document.getElementById("cboxKelurahan");
            var length = x.options.length;
            for (i = length; i > 0; i--) {
                x.options[i] = null;
            }
            document.getElementById("loading").style.zIndex = "-9999";
            document.getElementById("loading").style.opacity = "0";
        }
    });
});

$(document).on("change","#cboxKelurahan",function(){
    document.getElementById("loading").style.zIndex = "9999";
    document.getElementById("loading").style.opacity = "0.8";
    var nilai1 = $("#cboxProv").val();
    var nilai2 = $("#cboxKotakab").val();
    var nilai3 = $("#cboxKecamatan").val();
    var nilai4 = $(this).val();
    
    $.ajax({
        type : "post",
        url  : "{{ route('admin.kodepos') }}",
        data : { _token: "{{csrf_token()}}", nilai1 : nilai1, nilai2 : nilai2, nilai3 : nilai3, nilai4 : nilai4},
        dataType : "json",
        success :function(data){
            var i;
            document.getElementById("kodepos").value = "";
            document.getElementById("kodepos").value = data['getdatakodepos'][0]['kodepos'];
            posd = data['getdatakodepos'][0]['kodepos'];
            document.getElementById("loading").style.zIndex = "-9999";
            document.getElementById("loading").style.opacity = "0";
        },
        error : function(data){
            document.getElementById("kodepos").value = "";
            document.getElementById("loading").style.zIndex = "-9999";
            document.getElementById("loading").style.opacity = "0";
        }
    });
});


$(document).ready(function() {
    $('.js-basic-example').DataTable({
        responsive: true
    });

    //Exportable table
    $('.js-exportable').DataTable({
        dom: 'Bfrtip',
        responsive: true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    $('#datePicker').datetimepicker({
        format: 'YYYY-MM-DD'
    });
});
</script>
<!-- JQuery Steps Plugin Js -->
<script src="{{{ URL::asset('adminbsb/plugins/jquery-steps/jquery.steps.js') }}}"></script>

<!-- Sweet Alert Plugin Js -->
<script src="{{{ URL::asset('adminbsb/plugins/sweetalert/sweetalert.min.js') }}}"></script>

<!-- Custom JS -->
<script src="{{{ URL::asset('adminbsb/js/pages/forms/form-wizard.js') }}}"></script>
<script src="{{{ URL::asset('adminbsb/js/pages/forms/basic-form-elements.js') }}}"></script>

<!-- Autosize Plugin Js -->
<script src="{{{ URL::asset('adminbsb/plugins/autosize/autosize.js') }}}"></script>

<!-- Moment Plugin Js -->
<script src="{{{ URL::asset('adminbsb/plugins/momentjs/moment.js') }}}"></script>

<!-- Bootstrap Material Datetime Picker Plugin Js -->
<script src="{{{ URL::asset('adminbsb/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}}"></script>

<!-- Dropzone Plugin Js -->
<script src="{{{ URL::asset('adminbsb/plugins/dropzone/dropzone.js') }}}"></script>

<!-- Bootstrap Colorpicker Js -->
<script src="{{{ URL::asset('adminbsb/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js') }}}"></script>

<script src="{{{ URL::asset('adminbsb/js/pages/forms/advanced-form-elements.js') }}}"></script>
@endsection
@include('layouts.footer')