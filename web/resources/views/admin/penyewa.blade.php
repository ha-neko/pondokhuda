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

<!-- TABLE -->
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>
                    DATA PENYEWA
                </h2>
                <ul class="header-dropdown m-r--6">
                    <li class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons">more_vert</i>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <li>
                                <a href="{{ route('admin.createpenyewaview') }}">
                                    <i class="material-icons">add</i> <span class="icon-name">Penyewa Baru</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.pindahkamar') }}">
                                    <i class="material-icons">directions_run</i> <span class="icon-name">Pindah Kamar</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.berhentisewa') }}">
                                    <i class="material-icons">block</i> <span class="icon-name">Stop Penyewa</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover js-basic-example dataTable">
                        <thead>
                            <tr>
                                <tr>
                                <th>No Kamar</th>
                                <th>Nama Penyewa</th>
                                <th>Kirim Ulang Email</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($penyewa['datapenyewa'] != null)
                            @foreach($penyewa['datapenyewa'] as $p)
                            <tr>
                                <td>{{ $p['nokamar'] }}</td>
                                <td>{{ $p['nama'] }}</td>
                                <td>
                                    <a class="btn btn-danger" onclick="load(); resendEmail('{{ $p["kode"] }}');" href="javascript:void(0);">Email Pendaftaran</a>
                                    <a class="btn btn-success" onclick="load(); resendKwitansi('{{ $p["kode"] }}');" href="javascript:void(0);">Kwitansi Pembayaran Terakhir</a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.editpenyewa', $p['kode']) }}">
                                        <button type="button" class="btn btn-warning waves-effect">
                                            Edit / Lengkapi Data
                                        </button>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>
<!-- #END# TABLE -->


<!-- MODAL -->
<!-- #END# MODAL -->
<div class="loading" id="loading">Loading&#8230;</div>
@endsection
@section('js-content')
<script src="{{{ URL::asset('adminbsb/js/pages/ui/modals.js') }}}"></script>
<script type="text/javascript">

function load()
{
    document.getElementById("loading").style.zIndex = "999";
    document.getElementById("loading").style.opacity = "0.8";
    showLoadingMessage();
}

function showLoadingMessage() {
    swal({
        title: "Harap tunggu",
        text: "Sedang mengirim email",
        type: "info",
        showConfirmButton: false
    });
}

var bayar = 0;
function ttl() {
    var periodebayar = document.getElementById("periodebayar").value;
    var hargakamar = document.getElementById("hargakamar").value;
    var diskon = document.getElementById("diskon").value;
    if (parseInt(document.getElementById("diskon").value) < 0) {
        document.getElementById("diskon").value = 0;
    }
    if (parseInt(document.getElementById("diskon").value) > parseInt(bayar)) {
        document.getElementById("diskon").value = bayar;
    }
    if (parseInt(document.getElementById("periodebayar").value) < 1) {
        document.getElementById("periodebayar").value = 1;
    }
    document.getElementById("totalharga").value = (parseInt(document.getElementById("periodebayar").value) * parseInt(hargakamar)) - parseInt(document.getElementById("diskon").value);
    if (parseInt(document.getElementById("totalharga").value) < 0) {
        document.getElementById("totalharga").value = 0;
    }
    document.getElementById("totalbayar").value = document.getElementById("totalharga").value;
}
function byr() {
    if (parseInt(document.getElementById("totalbayar").value) > parseInt(document.getElementById("totalharga").value)) {
        document.getElementById("totalbayar").value = document.getElementById("totalharga").value;
    }
    if (parseInt(document.getElementById("totalbayar").value) < 0) {
        document.getElementById("totalbayar").value = 0;
    }
}

$(document).on("change", "#nomorkamar", function(){
    var nilai = $(this).val();
    $.ajax({
        type : "post",
        url  : "{{ route('admin.hargakamar') }}",
        data : { _token: "{{csrf_token()}}", nilai : nilai},
        dataType : "json",
        success :function(data){
            if (data['jumlahpenyewa'] != "0") {
                document.getElementById("hargakamar").value = data['hargakamar'];
                document.getElementById("periodebayar").value = 1;
                document.getElementById("diskon").value = 0;
                document.getElementById("totalharga").value = data['hargakamar'];
                document.getElementById("hargakamar").removeAttribute("readonly");
                document.getElementById("nokamar").value = data['nokamar'];
                document.getElementById("kamarterisi").value = "true";
                bayar = data['hargakamar'];

            }
            else {
                document.getElementById("hargakamar").value = data['hargakamar'];
                document.getElementById("periodebayar").value = 1;
                document.getElementById("diskon").value = 0;
                document.getElementById("totalharga").value = data['hargakamar'];
                document.getElementById("hargakamar").setAttribute("readonly", "true");
                document.getElementById("nokamar").value = data['nokamar'];
                document.getElementById("kamarterisi").value = "false";
                bayar = data['hargakamar'];
            }
        },
        error : function(data){
            document.getElementById("hargakamar").value = 0;
            document.getElementById("periodebayar").value = 0;
            document.getElementById("diskon").value = 0;
        }
    });
});
$(document).on("change","#cboxProv",function(){
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
        }
    });
});

$(document).on("change","#cboxKotakab",function(){
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
        },
        error : function(data){
            document.getElementById("cboxKecamatan").disabled = true;
            document.getElementById("cboxKelurahan").disabled = true;
            var x = document.getElementById("cboxKecamatan");
            var length = x.options.length;
            for (i = length; i > 0; i--) {
                x.options[i] = null;
            }
        }
    });
});

$(document).on("change","#cboxKecamatan",function(){
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
        },
        error : function(data){
            document.getElementById("cboxKelurahan").disabled = true;
            var x = document.getElementById("cboxKelurahan");
            var length = x.options.length;
            for (i = length; i > 0; i--) {
                x.options[i] = null;
            }
        }
    });
});

$(document).on("change","#cboxKelurahan",function(){
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
        },
        error : function(data){
            document.getElementById("kodepos").value = "";
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

function resendEmail(kode) {

    $.ajax({
        type : "post",
        url  : "{{ route('admin.penyewa-resend_email_daftar') }}",
        data : { _token: "{{csrf_token()}}", kode : kode },
        dataType : "json",
        success :function(data){
            showEmailMessage();
        },
        error : function(data){
            failEmailMessage();
        }
    });

    document.getElementById("loading").style.zIndex = "-999";
}

function resendKwitansi(kode) {

    $.ajax({
        type : "post",
        url  : "{{ route('admin.penyewa-resend_kwitansi') }}",
        data : { _token: "{{csrf_token()}}", kode : kode },
        dataType : "json",
        success :function(data){
            showKwitansiMessage();
        },
        error : function(data){
            failKwitansiMessage();
        }
    });

    document.getElementById("loading").style.zIndex = "-999";
}
function showEmailMessage() {
    swal("Email Pendaftaran Sudah Dikirim Ulang",
    "Silahkan cek kembali email untuk melihat kode dan pin", "success");
}

function failEmailMessage() {
    swal("Email Pendaftaran Gagal Dikirim",
    "Harap hubungi pihak developer untuk detail lebih lanjut", "error");
}

function showKwitansiMessage() {
    swal("Kwitansi Sudah Dikirim Ulang",
    "Silahkan cek email untuk melihat kwitansi pembayaran terakhir", "success");
}

function failKwitansiMessage() {
    swal("Kwitansi Gagal Dikirim",
    "Harap hubungi pihak developer untuk detail lebih lanjut", "error");
}
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