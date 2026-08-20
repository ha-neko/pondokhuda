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
        <form onSubmit="return validate()" id="form" action="{{route('admin.createpenyewa')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <!-- Nanti ilangin ini nih -->
            <div class="body">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header">
                                <h2>FORM PENGISIAN</h2>
                            </div>
                            <div class="body">
                                <h3>Informasi Penyewa</h3>
                                <fieldset>
                                    <center>
                                        <div id="imgfoto" style="background-image: url(''); width: 150px; height: 150px; background-size: cover; background-repeat: no-repeat; background-position: 50% 50%; border-radius: 100px; border: 5px solid #d4d4d6;"></div>
                                        <input name="image" id="foto" type="file" onchange="readURL(this);" accept="image/*"/>
                                        <span class="col-red"><label id="foto-info" for="foto" style="font-size:10px">Ukuran Foto Maks 900 KB.</label></span>
                                        <b>(Foto dapat dilewati)</b>
                                        <br>
                                    </center>
                                    <h4>Informasi Umum</h4>
                                    <fieldset>
                                        <div class="form-group form-float">
                                            <label class="form-label">No. KTP</label>
                                            <div class="form-line">
                                                <input type="number" id="noktp" class="form-control" name="noktp" placeholder="Masukkan nomor KTP" required>
                                            </div>
                                        </div>
                                        <div class="form-group form-float">
                                            <label class="form-label">Nama</label>
                                            <div class="form-line">
                                                <input type="text" id="nama" class="form-control" name="nama" placeholder="Masukkan nama" required>
                                            </div>
                                        </div>
                                        <div class="form-group form-float" style="display: none;">
                                            <label class="form-label">Tanggal lahir</label>
                                            <div class="form-line" >
                                                <input type="text" id="tgllhr" class="form-control datePicker" name="tgllhr" placeholder="Masukkan tanggal lahir">
                                            </div>
                                        </div>
                                        <div class="form-group form-float">
                                            <label class="form-label">No. HP/WA</label>
                                            <div class="form-line">
                                                <input type="number" id="nohp" class="form-control" name="nohp" placeholder="Masukkan nomor HP" required>
                                            </div>
                                        </div>
                                        <div class="form-group form-float" style="display: none;">
                                            <label for="me">Jenis Kelamin</label>
                                            <div class="form-group">
                                                <input type="radio" name="gender" id="male" checked="checked" class="with-gap" value="Pria">
                                                <label for="male">Laki - laki</label>

                                                <input type="radio" name="gender" id="female" class="with-gap" value="Wanita">
                                                <label for="female" class="m-l-20">Perempuan</label>
                                            </div>
                                        </div>
                                        <div class="form-group form-float">
                                            <label class="form-label">Email</label>
                                            <div class="form-line">
                                                <input type="email" id="email" class="form-control" name="email" placeholder="Masukkan email" required>
                                            </div>
                                        </div>
                                        <div class="form-group form-float" style="display: none;">
                                            <label class="form-label">Nama Orang tua/Wali</label>
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="namaortu" placeholder="Masukkan nama Orang Tua/Wali">
                                            </div>
                                        </div>
                                        <div class="form-group form-float" style="display: none;">
                                            <label class="form-label">No. HP Orang tua</label>
                                            <div class="form-line">
                                                <input type="number" class="form-control" name="nohportu" placeholder="Masukkan nomor HP Orang Tua">
                                            </div>
                                        </div>
                                        <div class="form-group form-float" style="display: none;">
                                            <label class="form-label">Lokasi Kuliah/Kerja</label>
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="tempatkuliahkerja" placeholder="Masukkan lokasi kuliah/kerja">
                                            </div>
                                        </div>
                                        <div class="form-group form-float" style="display: none;">
                                            <label class="form-label">Jurusan Kuliah</label>
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="jurkuliah" placeholder="Masukkan jurusan kuliah">
                                            </div>
                                        </div>
                                    </fieldset>

                                    <h4 style="display: none;">Informasi Domisili</h4>
                                    <fieldset style="display: none;">
                                        <div class="form-group form-float">
                                            <label class="form-label">Alamat kirim surat</label>
                                            <div class="form-line">
                                                <input type="text" name="alamat" class="form-control" placeholder="Masukkan alamat">
                                            </div>
                                        </div>
                                        <div class="form-group form-float">
                                            <label for="provinsi">Pilih Provinsi</label>
                                            <div class="form-line">
                                                <select class="form-control show-tick" name="provinsi" id="cboxProv">
                                                    <option value="">-- Pilih Provinsi --</option>
                                                    @foreach($provinsi['getdataprov'] as $k)
                                                    <option value="{{ $k['provinsi'] }}">{{ $k['provinsi'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group form-float">
                                            <label for="kotakab">Pilih Kota/Kabupaten</label>
                                            <div class="form-line">
                                                <select class="form-control show-tick" name="kotakab" id="cboxKotakab" disabled="">
                                                    <option value="">-- Pilih Kota/Kabupaten --</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group form-float">
                                            <label for="kecamatan">Pilih Kecamatan</label>
                                            <div class="form-line">
                                                <select class="form-control show-tick" name="kecamatan" id="cboxKecamatan" disabled="">
                                                    <option value="">-- Pilih Kecamatan --</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group form-float">
                                            <label for="kelurahan">Pilih Kelurahan</label>
                                            <div class="form-line">
                                                <select class="form-control show-tick" name="kelurahan" id="cboxKelurahan" disabled="">
                                                    <option value="">-- Pilih Kelurahan --</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group form-float">
                                            <label for="kodepos">Kode Pos</label>
                                            <div class="form-line">
                                                <input type="text" name="kodepos" class="form-control" id="kodepos" readonly="true" placeholder="Kode Pos">
                                            </div>
                                        </div>
                                    </fieldset>
                                </fieldset>

                                <input type="hidden" name="jml_penyewa" id="jml_penyewa" value="1">

                                <h3 id="label_penyewa2" style="display: none;">Informasi Penyewa Ke Dua</h3>
                                <fieldset id="penyewa2" style="display: none;">
                                    <center>
                                        <div id="imgfoto2" style="background-image: url(''); width: 150px; height: 150px; background-size: cover; background-repeat: no-repeat; background-position: 50% 50%; border-radius: 100px; border: 5px solid #d4d4d6;"></div>
                                        <input name="image2" id="foto2" type="file" onchange="readURL2(this);" accept="image/*"/>
                                        <span class="col-red"><label id="foto-info" for="foto" style="font-size:10px">Ukuran Foto Maks 900 KB.</label></span>
                                        <b>(Foto dapat dilewati)</b>
                                        <br>
                                    </center>
                                    <h4>Informasi Umum</h4>
                                    <fieldset>
                                        <div class="form-group form-float">
                                            <label class="form-label">No. KTP</label>
                                            <div class="form-line">
                                                <input type="number" id="noktp2" class="form-control" name="noktp2" placeholder="Masukkan nomor KTP" required>
                                            </div>
                                        </div>
                                        <div class="form-group form-float">
                                            <label class="form-label">Nama</label>
                                            <div class="form-line">
                                                <input type="text" id="nama2" class="form-control" name="nama2" placeholder="Masukkan nama" required>
                                            </div>
                                        </div>
                                        <div class="form-group form-float" style="display: none;">
                                            <label class="form-label">Tanggal lahir</label>
                                            <div class="form-line" >
                                                <input type="text" id="tgllhr2" class="form-control datePicker" name="tgllhr2" placeholder="Masukkan tanggal lahir">
                                            </div>
                                        </div>
                                        <div class="form-group form-float">
                                            <label class="form-label">No. HP/WA</label>
                                            <div class="form-line">
                                                <input type="number" id="nohp2" class="form-control" name="nohp2" placeholder="Masukkan nomor HP" required>
                                            </div>
                                        </div>
                                        <div class="form-group form-float" style="display: none;">
                                            <label for="me">Jenis Kelamin</label>
                                            <div class="form-group">
                                                <input type="radio" checked="checked" name="gender2" id="male2" class="with-gap" value="Pria">
                                                <label for="male2">Laki - laki</label>

                                                <input type="radio" name="gender2" id="female2" class="with-gap" value="Wanita">
                                                <label for="female2" class="m-l-20">Perempuan</label>
                                            </div>
                                        </div>
                                        <div class="form-group form-float">
                                            <label class="form-label">Email</label>
                                            <div class="form-line">
                                                <input type="email" id="email2" class="form-control" name="email2" placeholder="Masukkan email" required>
                                            </div>
                                        </div>
                                        <div class="form-group form-float" style="display: none;">
                                            <label class="form-label">Nama Orang tua/Wali</label>
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="namaortu2" placeholder="Masukkan nama Orang Tua/Wali">
                                            </div>
                                        </div>
                                        <div class="form-group form-float" style="display: none;">
                                            <label class="form-label">No. HP Orang tua</label>
                                            <div class="form-line">
                                                <input type="number" class="form-control" name="nohportu2" placeholder="Masukkan nomor HP Orang Tua">
                                            </div>
                                        </div>
                                        <div class="form-group form-float" style="display: none;">
                                            <label class="form-label">Lokasi Kuliah/Kerja</label>
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="tempatkuliahkerja2" placeholder="Masukkan lokasi kuliah/kerja">
                                            </div>
                                        </div>
                                        <div class="form-group form-float" style="display: none;">
                                            <label class="form-label">Jurusan Kuliah</label>
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="jurkuliah2" placeholder="Masukkan jurusan kuliah">
                                            </div>
                                        </div>
                                    </fieldset>

                                    <h4 style="display: none;">Informasi Domisili</h4>
                                    <fieldset style="display: none;">
                                        <div class="form-group form-float">
                                            <label class="form-label">Alamat kirim surat</label>
                                            <div class="form-line">
                                                <input type="text" name="alamat2" class="form-control" placeholder="Masukkan alamat">
                                            </div>
                                        </div>
                                        <div class="form-group form-float">
                                            <label for="provinsi">Pilih Provinsi</label>
                                            <div class="form-line">
                                                <select class="form-control show-tick" name="provinsi2" id="cboxProv2">
                                                    <option value="">-- Pilih Provinsi --</option>
                                                    @foreach($provinsi['getdataprov'] as $k)
                                                    <option value="{{ $k['provinsi'] }}">{{ $k['provinsi'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group form-float">
                                            <label for="kotakab">Pilih Kota/Kabupaten</label>
                                            <div class="form-line">
                                                <select class="form-control show-tick" name="kotakab2" id="cboxKotakab2" disabled="">
                                                    <option value="">-- Pilih Kota/Kabupaten --</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group form-float">
                                            <label for="kecamatan">Pilih Kecamatan</label>
                                            <div class="form-line">
                                                <select class="form-control show-tick" name="kecamatan2" id="cboxKecamatan2" disabled="">
                                                    <option value="">-- Pilih Kecamatan --</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group form-float">
                                            <label for="kelurahan">Pilih Kelurahan</label>
                                            <div class="form-line">
                                                <select class="form-control show-tick" name="kelurahan2" id="cboxKelurahan2" disabled="">
                                                    <option value="">-- Pilih Kelurahan --</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group form-float">
                                            <label for="kodepos">Kode Pos</label>
                                            <div class="form-line">
                                                <input type="text" name="kodepos2" class="form-control" id="kodepos2" readonly="true" placeholder="Kode Pos">
                                            </div>
                                        </div>
                                    </fieldset>
                                </fieldset>

                                <button type="button" id="addPenyewa" class="btn btn-primary" onclick="tambahDataPenyewa();">
                                    <i class="material-icons">add</i>
                                    <span class="icon-name">Tambah Data Penyewa Lainnya</span>
                                </button>

                                <h3>Informasi Sewa Kamar</h3>
                                <fieldset>
                                    <div class="form-group form-float">
                                        <label for="kelurahan">No. Kamar</label>
                                        <div class="form-line">
                                            <select class="form-control show-tick" name="kodekamar" id="nomorkamar" required>
                                                @if($kamar['kamar'] != null)
                                                <option value="">-- Pilih No. Kamar --</option>
                                                @foreach($kamar['kamar'] as $kt)
                                                @if($kt['jumlahpenyewa'] == null)
                                                <option value="{{ $kt['kodekamar'] }}">{{ $kt['nokamar'] }}</option>
                                                @else
                                                <option value="{{ $kt['kodekamar'] }}">{{ $kt['nokamar'] }} - Sudah ada {{ $kt['jumlahpenyewa'] }} Orang</option>
                                                @endif
                                                @endforeach
                                                @else
                                                <option value="">Belum ada kamar terdaftar</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <input type="hidden" name="nokamar" id="nokamar" value="">
                                    <input type="hidden" name="kamarterisi" id="kamarterisi" value="">
                                    <div class="form-group form-float">
                                        <label for="kodepos">Harga Kamar</label>
                                        <div class="form-line">
                                            <input type="hidden" name="hargakamar" class="form-control" id="hargakamar" placeholder="Harga Kamar" readonly="true">
                                            <input type="text" name="hk" oninput="ttl()" class="form-control" id="hk" placeholder="Harga Kamar" value="" required maxlength="19">
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <label for="kodepos">Periode Bayar</label>
                                        <div class="form-line">
                                            <input type="text" name="periodebayar" oninput="ttl()" onfocusout="set(this.value)" class="form-control" id="periodebayar" onkeypress="iuh(event)" placeholder="Periode Bayar" required>
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <label for="kodepos">Discount</label>
                                        <div class="form-line">
                                            <input type="hidden" name="diskon" id="diskon" value="0" required>
                                            <input type="text" name="dk" class="form-control" id="dk" oninput="ttl()" placeholder="Discount" value="0" required maxlength="19">
                                        </div>
                                    </div>
                                    <!-- <div class="form-group form-float">
                                        <label class="form-label">Tanggal Sanggup Bayar</label>
                                        <div class="form-line" >
                                            <input type="text" id='datePickers' class="form-control" name="tglsanggupbayar" placeholder="Masukkan tanggal bayar" required>
                                        </div>
                                    </div> -->
                                    <div class="form-group form-float">
                                        <label for="kodepos">Total Harga</label>
                                        <div class="form-line">
                                            <input type="hidden" id="ttlbyr">
                                            <input type="hidden" name="totalharga" class="form-control" id="totalharga" placeholder="Total Harga" readonly="true">
                                            <input type="text" name="th" id="th" class="form-control" placeholder="Total Harga" readonly="true" required>
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <label for="kodepos">Total Bayar</label>
                                        <div class="form-line">
                                            <input type="hidden" name="totalbayar" class="form-control" oninput="ttlbyr(this.value)" id="totalbayar" placeholder="Total Bayar" required>
                                            <input type="text" name="ttlbayar" class="form-control" oninput="byr()" oninput="ttlbyr(this.value)" id="ttlbayar" placeholder="Total Bayar" required  maxlength="19" readonly="true">
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <a href="{{ route('admin.penyewa') }}">
                                            <button class="btn waves-effect bg-{{Auth::user()->adminkost[$indexKost]['kost']['name_color']}}" type="button"><i class="material-icons">keyboard_arrow_left</i> <span class="icon-name">Kembali</span></button>
                                        </a>
                                        <button type="submit" class="btn waves-effect bg-{{Auth::user()->adminkost[$indexKost]['kost']['name_color']}}" onclick="load()"><i class="material-icons">check</i> <span class="icon-name">Tambah Penyewa</span></button>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="loading" id="loading">Loading&#8230;</div>
@endsection
@section('js-content')
<script src="{{{ URL::asset('adminbsb/js/pages/ui/modals.js') }}}"></script>
<script src="https://pondok-huda.com/js/validate.js" type="text/javascript"></script>
<script type="text/javascript">
function iuh(evt) {
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
var jml = 1;
function load() {
    if($("#form").valid())
    {
        document.getElementById("loading").style.zIndex = "999";
        document.getElementById("loading").style.opacity = "0.8";
        showInputMessage();   
    }
    // if (jml == 1) {
    //     if (document.getElementById("noktp").value != "" && document.getElementById("nama").value != "" && document.getElementById("nohp").value != "" && document.getElementById("email").value != "" && document.getElementById("nomorkamar").value != "" && document.getElementById("hargakamar").value != "" && document.getElementById("periodebayar").value != "" && document.getElementById("diskon").value != "" && document.getElementById("totalharga").value != "" && document.getElementById("totalbayar").value != "") {
    //         document.getElementById("loading").style.zIndex = "999";
    //         document.getElementById("loading").style.opacity = "0.8";
    //     }
    // }
    // else {
    //     if (document.getElementById("noktp").value != "" && document.getElementById("nama").value != "" && document.getElementById("nohp").value != "" && document.getElementById("email").value != "" && document.getElementById("noktp2").value != "" && document.getElementById("nama2").value != "" && document.getElementById("nohp2").value != "" && document.getElementById("email2").value != "" && document.getElementById("nomorkamar").value != "" && document.getElementById("hargakamar").value != "" && document.getElementById("periodebayar").value != "" && document.getElementById("diskon").value != "" && document.getElementById("totalharga").value != "" && document.getElementById("totalbayar").value != "") {
    //         document.getElementById("loading").style.zIndex = "999";
    //         document.getElementById("loading").style.opacity = "0.8";
    //     }
    // }
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
function tambahDataPenyewa() {

    if (document.getElementById('jml_penyewa').value == '1')
    {
        jml = 2;
        document.getElementById("penyewa2").style.display = '';
        document.getElementById('jml_penyewa').value = '2';

        document.getElementById("addPenyewa").className = "btn btn-danger";
        document.getElementById("addPenyewa").innerHTML = 
        '<i class="material-icons">close</i>' +
        '<span class="icon-name">Batal</span>';
    }
    else
    {
        jml = 1;
        document.getElementById("penyewa2").style.display = 'none';
        document.getElementById('jml_penyewa').value = '1';

        document.getElementById("addPenyewa").className = "btn btn-primary";
        document.getElementById("addPenyewa").innerHTML = 
        '<i class="material-icons">add</i>' +
        '<span class="icon-name">Tambah Data Penyewa Lainnya</span>';
    }

    var jmlPenyewa = document.getElementById('jml_penyewa').value;

    $.ajax({
        type : "post",
        url  : "{{ route('admin.getkamarcreate') }}",
        data : { _token: "{{csrf_token()}}", jmlpenyewa : jmlPenyewa},
        dataType : "json",
        success :function(data){

            var x = document.getElementById("nomorkamar");
            var length = x.options.length;
            for (i = length; i > 0; i--) {
                x.options[i] = null;
            }
            if(data['kamar'] != null)
            {
                for (i = 0; i < data['kamar'].length; i++) {
                    var x = document.getElementById("nomorkamar");
                    var option = document.createElement("option");
                    if (data['kamar'][i]['jumlahpenyewa'] != null) {
                        option.text = data['kamar'][i]['nokamar'] + " ada " + data['kamar'][i]['jumlahpenyewa'] + " orang";
                    }
                    else {
                        option.text = data['kamar'][i]['nokamar'];
                    }
                    
                    option.value = data['kamar'][i]['kodekamar'];
                    x.add(option);
                }
            }
        },
        error : function(data){
            alert('Error mengambil data: '+ JSON.stringify(data));
        }
    });
}

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
        document.getElementById("imgfoto").style.backgroundImage = "url('')";
        showConfirmMessage();
    }
}

function readURL2(input) {
    var file = document.getElementById("foto2").files[0];
    if(file.size <= 1000000) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                document.getElementById("imgfoto2").style.backgroundImage = "url('" + e.target.result + "')";
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
    else {
        document.getElementById("foto2").value = "";
        document.getElementById("imgfoto2").style.backgroundImage = "url('')";
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

var bayar = 0;
var tthgr = 0;
function set(num) {
    if (num == null || num == "") {
        document.getElementById("periodebayar").value = "1";
    }
}
function ttl() {
    // VARIABLE
    var periodebayar = document.getElementById("periodebayar").value;
    var hargakamar = document.getElementById("hargakamar").value;
    
    // END VARIABLE

    // HARGA KAMAR
    document.getElementById("hargakamar").value = parseFloat(document.getElementById("hk").value.replace(/[^0-9-.]/g, ''));
    var hk = document.getElementById("hargakamar").value;
    document.getElementById("hk").value = "" + hk.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
    if (document.getElementById("hargakamar").value == "NaN" || document.getElementById("hargakamar").value == null) {
        document.getElementById("hk").value = "0";
        document.getElementById("hargakamar").value = 0;
        document.getElementById("totalharga").value = 0;
    }
    bayar = document.getElementById("hargakamar").value;
    // END HARGA KAMAR

    // DISCOUNT
    document.getElementById("diskon").value = parseFloat(document.getElementById("dk").value.replace(/[^0-9-.]/g, ''));
    if (parseInt(document.getElementById("dk").value) < 0 || document.getElementById("diskon").value == "") {
        document.getElementById("diskon").value = 0;
        document.getElementById("totalharga").value = 0;
    }
    if (document.getElementById("dk").value == "" || document.getElementById("dk").value == "NaN") {
        document.getElementById("dk").value = 0;
        document.getElementById("diskon").value = 0;
        document.getElementById("totalharga").value = 0;
    }
    if (parseInt(document.getElementById("diskon").value) >= parseInt(document.getElementById("ttlbyr").value)) {
        document.getElementById("diskon").value = document.getElementById("ttlbyr").value;
    }
    var diskon = document.getElementById("diskon").value;
    document.getElementById("dk").value = "" + diskon.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
    // END DISCOUNT

    // PERIODE BAYAR
    if (parseInt(document.getElementById("periodebayar").value) < 1) {
        document.getElementById("periodebayar").value = 1;
    }
    // END PRIODE BAYAR

    // TOTAL HARGA
    if (document.getElementById("periodebayar").value == "" || document.getElementById("periodebayar").value == null) {
        document.getElementById("totalharga").value = bayar;
        document.getElementById("ttlbyr").value = bayar;
    }
    else {
        document.getElementById("totalharga").value = (parseInt(document.getElementById("periodebayar").value) * parseInt(document.getElementById("hargakamar").value)) - parseInt(document.getElementById("diskon").value);
        document.getElementById("ttlbyr").value = bayar * parseInt(document.getElementById("periodebayar").value);
    }
    
    document.getElementById("th").value = document.getElementById("totalharga").value;
    var ttlh = document.getElementById("totalharga").value;
    if (parseInt(document.getElementById("totalharga").value) < 0) {
        document.getElementById("totalharga").value = 0;
    }
    document.getElementById("th").value = "" + ttlh.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
    document.getElementById("totalbayar").value = document.getElementById("totalharga").value;
    // END TOTAL HARGA

    // TOTAL BAYAR
    document.getElementById("ttlbayar").value = document.getElementById("totalbayar").value;
    var tb = document.getElementById("totalbayar").value;
    document.getElementById("ttlbayar").value = "" + tb.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
    if (document.getElementById("totalbayar").value == null || document.getElementById("totalbayar").value == "NaN") {
        document.getElementById("totalbayar").value = 0;
    }
    // END TOTAL BAYAR
}
function byr() {
    var d = parseFloat(document.getElementById("ttlbayar").value.replace(/[^0-9-.]/g, ''));
    document.getElementById("totalbayar").value = d;
    if (parseInt(document.getElementById("totalbayar").value) > parseInt(document.getElementById("totalharga").value)) {
        document.getElementById("totalbayar").value = document.getElementById("totalharga").value;
    }
    if (parseInt(document.getElementById("totalbayar").value) < 0) {
        document.getElementById("totalbayar").value = 0;
    }
    if (document.getElementById("totalbayar").value == null || document.getElementById("totalbayar").value == "NaN") {
        document.getElementById("totalbayar").value = 0;
    }
    document.getElementById("ttlbayar").value = "" + document.getElementById("totalbayar").value.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
}

$(document).on("change", "#nomorkamar", function(){
    document.getElementById("loading").style.zIndex = "999";
    document.getElementById("loading").style.opacity = "0.8";
    var nilai = $(this).val();
    $.ajax({
        type : "post",
        url  : "{{ route('admin.hargakamar') }}",
        data : { _token: "{{csrf_token()}}", nilai : nilai},
        dataType : "json",
        success :function(data){
            if (data['jumlahpenyewa'] != "0") {
                document.getElementById("hk").value = "" + data['hargakamar'].toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
                document.getElementById("hargakamar").value = data['hargakamar']
                document.getElementById("periodebayar").value = 1;
                document.getElementById("diskon").value = 0;
                document.getElementById("dk").value = "0";
                document.getElementById("totalharga").value = data['hargakamar'];
                document.getElementById("th").value = data['hargakamar'].toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
                document.getElementById("hargakamar").removeAttribute("readonly");
                document.getElementById("nokamar").value = data['nokamar'];
                document.getElementById("kamarterisi").value = "true";
                bayar = data['hargakamar'];
                document.getElementById("totalbayar").value = bayar * parseInt(document.getElementById("periodebayar").value);
                document.getElementById("ttlbayar").value = "" + document.getElementById("totalbayar").value.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
            }
            else {
                document.getElementById("hk").value = "" + data['hargakamar'].toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
                document.getElementById("hargakamar").value = data['hargakamar']
                document.getElementById("periodebayar").value = 1;
                document.getElementById("diskon").value = 0;
                document.getElementById("dk").value = "0";
                document.getElementById("totalharga").value = data['hargakamar'];
                document.getElementById("th").value = data['hargakamar'].toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
                document.getElementById("hargakamar").removeAttribute("readonly");
                document.getElementById("nokamar").value = data['nokamar'];
                document.getElementById("kamarterisi").value = "false";
                bayar = data['hargakamar'];
                ttlhgr = data['hargakamar'];
                document.getElementById("totalbayar").value = bayar * parseInt(document.getElementById("periodebayar").value);
                document.getElementById("ttlbayar").value = "" + document.getElementById("totalbayar").value.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
            }
            
            document.getElementById("loading").style.zIndex = "-999";
            document.getElementById("loading").style.opacity = "0";
        },
        error : function(data){
            document.getElementById("hargakamar").value = 0;
            document.getElementById("periodebayar").value = 0;
            document.getElementById("diskon").value = 0;
        }
    });
});
// $(document).on("change","#cboxProv",function(){
//     var nilai = $(this).val();
//     $.ajax({
//         type : "post",
//         url  : "{{ route('admin.kotakab') }}",
//         data : { _token: "{{csrf_token()}}", nilai : nilai},
//         dataType : "json",
//         success :function(data){
//             var i;
//             var x = document.getElementById("cboxKotakab");
//             var length = x.options.length;
//             for (i = length; i > 0; i--) {
//                 x.options[i] = null;
//             }
//             var x = document.getElementById("cboxKecamatan");
//             var length = x.options.length;
//             for (i = length; i > 0; i--) {
//                 x.options[i] = null;
//             }
//             document.getElementById("cboxKecamatan").disabled = true;
//             var x = document.getElementById("cboxKelurahan");
//             var length = x.options.length;
//             for (i = length; i > 0; i--) {
//                 x.options[i] = null;
//             }
//             document.getElementById("cboxKelurahan").disabled = true;
//             document.getElementById("kodepos").value = "";
//             for (i = 0; i < data['getdatakotakab'].length; i++) {
//                 document.getElementById("cboxKotakab").disabled = false;
//                 var x = document.getElementById("cboxKotakab");
//                 var option = document.createElement("option");
//                 option.text = data['getdatakotakab'][i]['kotakab'];
//                 option.value = data['getdatakotakab'][i]['kotakab'];
//                 x.add(option);
//             }
//         },
//         error : function(data){
//             document.getElementById("cboxKotakab").disabled = true;
//             document.getElementById("cboxKecamatan").disabled = true;
//             document.getElementById("cboxKelurahan").disabled = true;
//             var x = document.getElementById("cboxKotakab");
//             var length = x.options.length;
//             for (i = length; i > 0; i--) {
//                 x.options[i] = null;
//             }
//         }
//     });
// });

// $(document).on("change","#cboxKotakab",function(){
//     var nilai1 = $("#cboxProv").val();
//     var nilai2 = $(this).val();
    
//     $.ajax({
//         type : "post",
//         url  : "{{ route('admin.kecamatan') }}",
//         data : { _token: "{{csrf_token()}}", nilai1 : nilai1, nilai2 : nilai2},
//         dataType : "json",
//         success :function(data){
//             var i;
//             var x = document.getElementById("cboxKecamatan");
//             var length = x.options.length;
//             for (i = length; i > 0; i--) {
//                 x.options[i] = null;
//             }
//             var x = document.getElementById("cboxKelurahan");
//             var length = x.options.length;
//             for (i = length; i > 0; i--) {
//                 x.options[i] = null;
//             }
//             document.getElementById("cboxKelurahan").disabled = true;
//             document.getElementById("kodepos").value = "";
//             for (i = 0; i < data['getdatakecamatan'].length; i++) {
//                 document.getElementById("cboxKecamatan").disabled = false;
//                 var x = document.getElementById("cboxKecamatan");
//                 var option = document.createElement("option");
//                 option.text = data['getdatakecamatan'][i]['kecamatan'];
//                 option.value = data['getdatakecamatan'][i]['kecamatan'];
//                 x.add(option);
//             }
//         },
//         error : function(data){
//             document.getElementById("cboxKecamatan").disabled = true;
//             document.getElementById("cboxKelurahan").disabled = true;
//             var x = document.getElementById("cboxKecamatan");
//             var length = x.options.length;
//             for (i = length; i > 0; i--) {
//                 x.options[i] = null;
//             }
//         }
//     });
// });

// $(document).on("change","#cboxKecamatan",function(){
//     var nilai1 = $("#cboxProv").val();
//     var nilai2 = $("#cboxKotakab").val();
//     var nilai3 = $(this).val();
    
//     $.ajax({
//         type : "post",
//         url  : "{{ route('admin.kelurahan') }}",
//         data : { _token: "{{csrf_token()}}", nilai1 : nilai1, nilai2 : nilai2, nilai3 : nilai3},
//         dataType : "json",
//         success :function(data){
//             var i;
//             var x = document.getElementById("cboxKelurahan");
//             var length = x.options.length;
//             for (i = length; i > 0; i--) {
//                 x.options[i] = null;
//             }
//             document.getElementById("kodepos").value = "";
//             for (i = 0; i < data['getdatakelurahan'].length; i++) {
//                 document.getElementById("cboxKelurahan").disabled = false;
//                 var x = document.getElementById("cboxKelurahan");
//                 var option = document.createElement("option");
//                 option.text = data['getdatakelurahan'][i]['kelurahan'];
//                 option.value = data['getdatakelurahan'][i]['kelurahan'];
//                 x.add(option);
//             }
//         },
//         error : function(data){
//             document.getElementById("cboxKelurahan").disabled = true;
//             var x = document.getElementById("cboxKelurahan");
//             var length = x.options.length;
//             for (i = length; i > 0; i--) {
//                 x.options[i] = null;
//             }
//         }
//     });
// });

// $(document).on("change","#cboxKelurahan",function(){
//     var nilai1 = $("#cboxProv").val();
//     var nilai2 = $("#cboxKotakab").val();
//     var nilai3 = $("#cboxKecamatan").val();
//     var nilai4 = $(this).val();
    
//     $.ajax({
//         type : "post",
//         url  : "{{ route('admin.kodepos') }}",
//         data : { _token: "{{csrf_token()}}", nilai1 : nilai1, nilai2 : nilai2, nilai3 : nilai3, nilai4 : nilai4},
//         dataType : "json",
//         success :function(data){
//             var i;
//             document.getElementById("kodepos").value = "";
//             document.getElementById("kodepos").value = data['getdatakodepos'][0]['kodepos'];
//         },
//         error : function(data){
//             document.getElementById("kodepos").value = "";
//         }
//     });
// });
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
    $('.datePicker').datetimepicker({
        format: 'YYYY-MM-DD'
    });
    $('#datePickers').datetimepicker({
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