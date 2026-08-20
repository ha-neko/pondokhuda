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
                <div class="row clearfix">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="body">
        						<form onSubmit="return validate()" id="form" action="{{ route('admin.stopsewa') }}" method="post">
        						@csrf
                                    <h3>Penyewa Berhenti Sewa</h3>
                                	<div class="form-group form-float">
                                        <label for="nokamar">No. Kamar</label>
                                        <div class="form-line">
                                            <select class="form-control show-tick" name="nokamar" id="nomorkamar" required>
                                                @if($nokamar['kamar'] != null)
                                                <option value="">-- Pilih Kamar --</option>
                                                @foreach($nokamar['kamar'] as $k)
                                                <option value="{{ $k['kode_kamar'] }}">{{ $k['nokamar'] }}</option>
                                                @endforeach
                                                @else
                                                <option value="">Belum ada kamar terdaftar</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <input type="hidden" name="kodesewa" id="kodesewa">
                                    <input type="hidden" name="nomerkamar" id="nomerkamar">
                                    <label for="penyewa" id="labelpenyewa" style="display: none;">Pilih Penyewa</label>
                                    <div class="demo-checkbox required" id="checkpenyewa">
                                    </div>
                                    <div id="alertRequired"></div>
                                    <input type="hidden" name="kodepenyewa[]" id="penyewa1" value="">
                                    <input type="hidden" name="kodepenyewa[]" id="penyewa2" value="">
                                    <input type="hidden" name="namapenyewa[]" id="namapenyewa1" value="">
                                    <input type="hidden" name="namapenyewa[]" id="namapenyewa2" value="">
                                    <div class="form-group form-float">
                                        <label for="tanggalpembayaran">Tanggal Pembayaran Terakhir</label>
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="tanggalpembayaran" id="tanggalpembayaran" placeholder="Tanggal Pembayaran Terakhir" required readonly="true">
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <label for="tangalmulai">Periode Sewa</label>
                                        <div class="form-line disabled">
                                            <input type="text" class="form-control" name="periodesewa" id="periodesewa" placeholder="Periode Sewa" required readonly="true">
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <label for="periodebayar">Periode Bayar</label>
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="periodebayars" id="periodebayars" placeholder="Tanggal Mulai" required readonly="true">
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <label for="hargakosperbulan">Harga Kamar</label>
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="hargakosperbulan" id="hargakosperbulan" placeholder="Tanggal Mulai" required readonly="true">
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <a href="{{ route('admin.penyewa') }}">
                                        <button class="btn bg-{{Auth::user()->adminkost[$indexKost]['kost']['name_color']}} waves-effect" type="button"><i class="material-icons">keyboard_arrow_left</i> <span class="icon-name">Kembali</span></button>
                                        </a>
                                        <button type="submit" class="btn waves-effect bg-{{Auth::user()->adminkost[$indexKost]['kost']['name_color']}}" id="btnsmt" onclick="load()"><i class="material-icons">check</i><span class="icon-name">Berhenti Sewa</span></button>
                                    </div>
                                    <br>
                                </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
</div>
<div class="loading" id="loading">Loading&#8230;</div>
@endsection
@section('js-content')
<script src="{{{ URL::asset('adminbsb/js/pages/ui/modals.js') }}}"></script>
<script src="https://pondok-huda.com/js/validate.js" type="text/javascript"></script>
<script type="text/javascript">
// function load() {
//     if (document.getElementById("nomorkamar").value != "") {
//         if (document.getElementById("basic_checkbox_1")) {
//             if (document.getElementById("basic_checkbox_0").checked == true || document.getElementById("basic_checkbox_1").checked == true) {
//                 if (document.getElementById("tanggalmulai").value != "" && document.getElementById("tanggalpembayaran").value != "" && document.getElementById("periodebayars").value != "" && document.getElementById("hargakosperbulan").value != "") {
//                     document.getElementById("loading").style.zIndex = "999";
//                     document.getElementById("loading").style.opacity = "0.8";
//                 }
//             }
//             else {
//                 alert("Belum ada penyewa yang dipilih");
//             }
//         }
//         else {
//             if (document.getElementById("basic_checkbox_0").checked == true) {
//                 if (document.getElementById("tanggalmulai").value != "" && document.getElementById("tanggalpembayaran").value != "" && document.getElementById("periodebayars").value != "" && document.getElementById("hargakosperbulan").value != "") {
//                     document.getElementById("loading").style.zIndex = "999";
//                     document.getElementById("loading").style.opacity = "0.8";
//                 }
//             }
//             else {
//                 alert("Belum ada penyewa yang dipilih");
//             }
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
var bayar = 0;
// function ttl() {
//     var periodebayar = document.getElementById("periodebayar").value;
//     var hargakamar = document.getElementById("hargakamar").value;
//     var diskon = document.getElementById("diskon").value;
//     if (parseInt(document.getElementById("diskon").value) < 0) {
//         document.getElementById("diskon").value = 0;
//     }
//     if (parseInt(document.getElementById("diskon").value) > parseInt(bayar)) {
//         document.getElementById("diskon").value = bayar;
//     }
//     if (parseInt(document.getElementById("periodebayar").value) < 1) {
//         document.getElementById("periodebayar").value = 1;
//     }
//     document.getElementById("totalharga").value = (parseInt(document.getElementById("periodebayar").value) * parseInt(hargakamar)) - parseInt(document.getElementById("diskon").value);
//     if (parseInt(document.getElementById("totalharga").value) < 0) {
//         document.getElementById("totalharga").value = 0;
//     }
//     document.getElementById("totalbayar").value = document.getElementById("totalharga").value;
// }
function byr() {
    if (parseInt(document.getElementById("totalbayar").value) > parseInt(document.getElementById("totalharga").value)) {
        document.getElementById("totalbayar").value = document.getElementById("totalharga").value;
    }
    if (parseInt(document.getElementById("totalbayar").value) < 0) {
        document.getElementById("totalbayar").value = 0;
    }
}
$(document).on("change", "#nomorkamar", function(){
    document.getElementById("loading").style.zIndex = "9999";
    document.getElementById("loading").style.opacity = "0.8";
    var nilai = $(this).val();
    $.ajax({
        type : "post",
        url  : "{{ route('admin.getdatasewakamar') }}",
        data : { _token: "{{csrf_token()}}", nilai : nilai },
        dataType : "json",
        success :function(data){
        	document.getElementById("checkpenyewa").innerHTML = "";
            for (i = 0; i < data['datasewa'].length; i++) {
            	document.getElementById("checkpenyewa").innerHTML += '<input type="checkbox" name="penyewa" onclick="test(this.id);" id="basic_checkbox_'+i+'" value="'+data['datasewa'][i]['kode_penyewa']+'" required /><label for="basic_checkbox_'+i+'">' + data['datasewa'][i]['nama_penyewa'] + '</label><br>';
            }
            document.getElementById("checkpenyewa").innerHTML += '<br>';
            document.getElementById("kodesewa").value = data['datasewa'][0]['kode_sewa'];
            document.getElementById("nomerkamar").value = $("#nomorkamar option:selected").text();
            document.getElementById("periodesewa").value = data['datasewa'][0]['periode_sewa'];
            document.getElementById("tanggalpembayaran").value = data['datasewa'][0]['tgl_pembayaran'];
            document.getElementById("periodebayars").value = data['datasewa'][0]['periode_bayar'];
            document.getElementById("hargakosperbulan").value = data['datasewa'][0]['harga_perbulan'];
            document.getElementById("loading").style.zIndex = "-9999";
            document.getElementById("loading").style.opacity = "0";
        },
        error : function(data){
            document.getElementById("checkpenyewa").innerHTML = "";
            document.getElementById("kodesewa").value = "";
            document.getElementById("nomerkamar").value = "";
            document.getElementById("periodesewa").value = "";
            document.getElementById("tanggalpembayaran").value = "";
            document.getElementById("periodebayars").value = "";
            document.getElementById("hargakosperbulan").value = "";
            document.getElementById("showBaru").style.display = "none";
            document.getElementById("labelpenyewa").style.display = "none";
            document.getElementById("loading").style.zIndex = "-9999";
            document.getElementById("loading").style.opacity = "0";
        }
    });
});
$(document).ready(function(){
    $('#form').validate({
        rules: {
                'kodepenyewa[]': {
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
function test(ea){
    var penyewaCount = document.getElementsByName("penyewa").length;
    var penyewaCheck1 = 0;
    var penyewaCheck2 = 0;
    var penyewaCheckTotal = 0;
    var penyewa = [];
    for (var i = 0; i < penyewaCount; i++) {
        if (document.getElementsByName("penyewa")[i].checked) {
            document.getElementById("penyewa"+(i+1)).value = document.getElementById("basic_checkbox_"+i).value;
            document.getElementById("namapenyewa"+(i+1)).value = $("#basic_checkbox_"+i).next("label").html();
        }
        else{
            document.getElementById("penyewa"+(i+1)).value = "";
            document.getElementById("namapenyewa"+(i+1)).value = "";
        }
    }

    if (document.getElementById("penyewa1").value != '') {
        penyewaCheck1 = 1;
    }
    else{
        penyewaCheck1 = 0;
    }

    if (document.getElementById("penyewa2").value != '') {
        penyewaCheck2 = 1;
        document.getElementById("basic_checkbox_0").removeAttribute("required");
        document.getElementById("basic_checkbox_1").removeAttribute("required");
    }
    else{
        penyewaCheck2 = 0;
    }

    penyewaCheckTotal = penyewaCheck1+penyewaCheck2;
    if (penyewaCheckTotal > 0) {
        document.getElementById("basic_checkbox_0").removeAttribute("required");
        document.getElementById("basic_checkbox_1").removeAttribute("required");
    }
    else {
        document.getElementById("basic_checkbox_0").setAttribute("required", "true");
        document.getElementById("basic_checkbox_1").setAttribute("required", "true");
    }
};
// PINDAH KAMAR BARU
function ttl() {
    // VARIABLE
    var periodebayar = document.getElementById("periodebayar").value;
    var hargakamar = document.getElementById("hargakamar").value;
    
    // END VARIABLE

    // HARGA KAMAR
    document.getElementById("hargakamar").value = parseFloat(document.getElementById("hk").value.replace(/[^0-9-.]/g, ''));
    var hk = document.getElementById("hargakamar").value;
    document.getElementById("hk").value = "Rp " + hk.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
    if (document.getElementById("hargakamar").value == "NaN" || document.getElementById("hargakamar").value == null) {
        document.getElementById("hk").value = "Rp 0";
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
    if (document.getElementById("dk").value == "Rp " || document.getElementById("dk").value == "Rp NaN") {
        document.getElementById("dk").value = 0;
        document.getElementById("diskon").value = 0;
        document.getElementById("totalharga").value = 0;
    }
    if (parseInt(document.getElementById("diskon").value) >= parseInt(document.getElementById("ttlbyr").value)) {
        document.getElementById("diskon").value = document.getElementById("ttlbyr").value;
    }
    var diskon = document.getElementById("diskon").value;
    document.getElementById("dk").value = "Rp " + diskon.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
    // END DISCOUNT

    // PERIODE BAYAR
    if (parseInt(document.getElementById("periodebayar").value) < 1) {
        document.getElementById("periodebayar").value = 1;
    }
    // END PRIODE BAYAR

    // TOTAL HARGA
    if (document.getElementById("periodebayar").value == "" || document.getElementById("periodebayar").value == null) {
        document.getElementById("totalharga").value = 0;
        document.getElementById("ttlbyr").value = 0;
    }
    else {
        document.getElementById("totalharga").value = ((parseInt(document.getElementById("periodebayar").value) * parseInt(document.getElementById("hargakamar").value)) + parseInt(document.getElementById("bayarperhari").value)) - parseInt(document.getElementById("diskon").value);
        document.getElementById("ttlbyr").value = ((document.getElementById("hargakamar").value * parseInt(document.getElementById("periodebayar").value)) + parseInt(document.getElementById("bayarperhari").value)) + parseInt(document.getElementById("bayarperhari").value);
        console.log("no null : " + document.getElementById("ttlbyr").value);
    }
    console.log("nothing : " + document.getElementById("ttlbyr").value);
    document.getElementById("th").value = document.getElementById("totalharga").value;
    var ttlh = document.getElementById("totalharga").value;
    if (parseInt(document.getElementById("totalharga").value) < 0) {
        document.getElementById("totalharga").value = 0;
    }
    document.getElementById("th").value = "Rp " + ttlh.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
    document.getElementById("totalbayar").value = document.getElementById("totalharga").value;
    // END TOTAL HARGA

    // TOTAL BAYAR
    document.getElementById("ttlbayar").value = document.getElementById("totalbayar").value;
    var tb = document.getElementById("totalbayar").value;
    document.getElementById("ttlbayar").value = "Rp " + tb.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
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
    document.getElementById("ttlbayar").value = "Rp " + document.getElementById("totalbayar").value.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
}

$(document).on("change", "#nomorkamars", function(){
    var nilai = $(this).val();
    $.ajax({
        type : "post",
        url  : "{{ route('admin.hargakamar') }}",
        data : { _token: "{{csrf_token()}}", nilai : nilai},
        dataType : "json",
        success :function(data){
            if (data['jumlahpenyewa'] != "0") {
                document.getElementById("hk").value = "Rp " + data['hargakamar'].toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
                document.getElementById("hargakamar").value = data['hargakamar']
                document.getElementById("periodebayar").value = 1;
                document.getElementById("diskon").value = 0;
                document.getElementById("dk").value = "Rp 0";
                document.getElementById("totalharga").value = data['hargakamar'];
                document.getElementById("th").value = data['hargakamar'].toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
                document.getElementById("hargakamar").removeAttribute("readonly");
                document.getElementById("nokamar").value = data['nokamar'];
                document.getElementById("kamarterisi").value = "true";
                bayar = data['hargakamar'];
                ttlhgr = data['hargakamar'];
                document.getElementById("ttlbyr").value = (bayar * parseInt(document.getElementById("periodebayar").value)) + parseInt(document.getElementById("bayarperhari").value);
            }
            else {
                document.getElementById("hk").value = "Rp " + data['hargakamar'].toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
                document.getElementById("hargakamar").value = data['hargakamar']
                document.getElementById("periodebayar").value = 1;
                document.getElementById("diskon").value = 0;
                document.getElementById("dk").value = "Rp 0";
                document.getElementById("totalharga").value = data['hargakamar'];
                document.getElementById("th").value = data['hargakamar'].toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
                document.getElementById("hargakamar").removeAttribute("readonly");
                document.getElementById("nokamar").value = data['nokamar'];
                document.getElementById("kamarterisi").value = "false";
                bayar = data['hargakamar'];
                ttlhgr = data['hargakamar'];
                document.getElementById("ttlbyr").value = (bayar * parseInt(document.getElementById("periodebayar").value)) + parseInt(document.getElementById("bayarperhari").value);
            }
            var d = new Date();
            if (document.getElementById("datePickers").value == null || document.getElementById("datePickers").value == "") {
                document.getElementById("bayarperhari").value = 0;
            }   
            else {
                var c = document.getElementById("datePickers").value;
                c = c.replace(/-/g, "");
                year = d.getFullYear();
                month = d.getMonth() + 1;
                if (month < 10) {
                    var mon = "0" + month;
                }
                date = d.getDate();
                if (date < 10) {
                    var det = "0" + date;
                }
                lastday = new Date(year, month, 0).getDate();
                var datenow = year.toString() + mon.toString() + det.toString();
                if (parseInt(c) < parseInt(datenow)) {
                    alert("Maaf tanggal tidak boleh kurang dari tanggal sekarang");
                    document.getElementById("datePickers").value = year.toString() + "-" + mon.toString() + "-" + det.toString();
                }
                var uus = parseInt(c) - parseInt(datenow);
                if (document.getElementById("hargakamar").value == null || document.getElementById("hargakamar").value == "") {
                    document.getElementById("bayarperhari").value = 0;
                }
                else {
                    var hph = parseInt(document.getElementById("hargakamar").value) / parseInt(lastday);
                    var hasil = Math.round(hph) * parseInt(uus);
                    if (hasil < 0) {
                        document.getElementById("bayarperhari").value = 0;
                    }
                    else {
                        document.getElementById("bayarperhari").value = hasil;
                    }
                }
            }
        },
        error : function(data){
            document.getElementById("hargakamar").value = 0;
            document.getElementById("periodebayar").value = 0;
            document.getElementById("diskon").value = 0;
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
    $('#datePickers').datetimepicker({
        format: 'YYYY-MM-DD'
    });
});
</script>
<!-- Light Gallery Plugin Js -->
<script src="{{{ URL::asset('adminbsb/plugins/light-gallery/js/lightgallery-all.js') }}}"></script>
<!-- Demo Js -->
<script src="{{{ URL::asset('adminbsb/js/demo.js') }}}"></script>
<!-- Custom Js -->
<script src="{{{ URL::asset('adminbsb/js/pages/medias/image-gallery.js') }}}"></script>
<!-- Bootstrap Material Datetime Picker Plugin Js -->
<script src="{{{ URL::asset('adminbsb/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}}"></script>
@endsection
@include('layouts.footer')