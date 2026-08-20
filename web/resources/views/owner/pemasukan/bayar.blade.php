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
    <form onSubmit="return validate()" id="form" action="{{ route('owner.bayar') }}" method="post">@csrf
    <div class="body">
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="body">
                        <div class="form-group form-float">
                            <label class="form-label">No. Kamar</label>
                            <div class="form-line">
                                <select type="text" id="kode_kamar" name="kode_kamar" class="form-control" required>
                                    @if($kamar['kamar'] != null)
                                    <option value="">-- Pilih Kamar --</option>
                                    @foreach($kamar['kamar'] as $k)
                                    <option value="{{ $k['kode_kamar'] }}">{{ $k['nokamar'] }}</option>
                                    @endforeach
                                    @else
                                    <option value="">Belum ada kamar terdaftar</option>
                                    @endif
                                </select>
                            </div>
                        </div><input type="hidden" id="nokamar" name="nokamar">
                        <div class="form-group form-float">
                            <label class="form-label">Nama Penyewa</label>
                            <div class="form-line">
                                <input type="text" id="namapenyewa" class="form-control" name="namapenyewa" placeholder="Nama Penyewa" readonly="true" required>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <label class="form-label">Periode Sewa</label>
                            <div class="form-line">
                                <input type="text" id="periodesewa" class="form-control" name="periodesewa" placeholder="Periode Sewa" readonly="true" required>
                            </div>
                        </div>
                        <div id="detailPembayaran" style="display: none;">
                            <input type="hidden" id="kode_sewa" class="form-control" name="kodesewa">
                            <div id="detail" style="display: none; border: 1px solid #000; padding: 10px 10px;">
                            <button type="button" class="btn bg-{{Auth::user()->ownerkost[$indexKost]['kost']['name_color']}} waves-effect" onclick="detail()" id="btndetail">Show Detail</button>
                                <div class="form-group form-float">
                                    <label class="form-label">No. KTP</label>
                                    <div class="form-line">
                                        <input type="text" id="noktp" class="form-control" name="noktp" placeholder="No. KTP" readonly="true">
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <label class="form-label">Tanggal Pembayaran</label>
                                    <div class="form-line">
                                        <input type="text" id="tglpembayaran" class="form-control" name="tglpembayaran" placeholder="Tanggal Pembayaran" readonly="true" required>
                                    </div>
                                </div>
                            </div>
                            <div id="baru">
                                <div class="form-group form-float">
                                    <label class="form-label">Periode Pembayaran</label>
                                    <div class="form-line">
                                        <input type="number" id="periodebayar" name="periodebayar" class="form-control" placeholder="Periode Bayar" oninput="isittl(this.id)" onchange="isittl(this.id)" required>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <label class="form-label">Harga Kos Perbulan</label>
                                    <div class="form-line">
                                        <input type="text" id="hargaperbulan" name="hargaperbulan" class="form-control" placeholder="Harga Kost Perbulan" readonly="true" required>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <label class="form-label">Denda</label>
                                    <div class="form-line">
                                        <input type="text" id="denda" name="denda" class="form-control" placeholder="Denda" oninput="isittl(this.id)" maxlength="19" required>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <label class="form-label">Discount</label>
                                    <div class="form-line">
                                        <input type="text" id="diskon" name="diskon" class="form-control" placeholder="Diskon" oninput="isittl(this.id)" required maxlength="19">
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <label class="form-label">Harga Total</label>
                                    <div class="form-line">
                                        <input type="text" id="hargatotal" name="hargatotal" class="form-control" placeholder="Harga Total" readonly="true" required>
                                    </div>
                                </div>
                            </div>
                            <div id="nyicil">
                                <div class="form-group form-float">
                                    <label class="form-label">Sisa Bayar Sebelumnya</label>
                                    <div class="form-line">
                                        <input type="text" id="sisa_bayar" name="sisa_bayar" class="form-control" placeholder="Diskon" required maxlength="19" readonly="true">
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="denda_sebelumnya" id="denda_sebelumnya">
                            <input type="hidden" name="diskon_sebelumnya" id="diskon_sebelumnya">
                            <input type="hidden" name="harga_pindah_sebelumnya" id="harga_pindah_sebelumnya">
                            <input type="hidden" name="total_harga_sebelumnya" id="total_harga_sebelumnya">
                            <input type="hidden" name="total_bayar_sebelumnya" id="total_bayar_sebelumnya">
                            <div class="form-group form-float">
                                <label class="form-label">Total Bayar</label>
                                <div class="form-line">
                                    <input type="text" oninput="uih(this.id)" id="totalbayar" name="totalbayar" class="form-control" placeholder="Total Bayar" required maxlength="19">
                                </div>
                            </div>
                            <div class="form-group form-float">
                                <label class="form-label">Metode Bayar</label>
                                <div class="form-line">
                                    <input name="metode_bayar" type="radio" class="with-gap" id="tunai" value="Tunai" checked="checked" required />
                                    <label for="tunai">Tunai</label>
                                    <input name="metode_bayar" type="radio" class="with-gap" value="Transfer" id="transfer" required />
                                    <label for="transfer">Transfer</label>
                                </div>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn bg-{{Auth::user()->ownerkost[$indexKost]['kost']['name_color']}} waves-effect" onclick="load()"><i class="material-icons">done</i> <span class="icon-name">Simpan</span></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
</div>
<div class="loading" id="loading">Loading&#8230;</div>
@endsection
@section('js-content')
<script src="{{{ URL::asset('adminbsb/js/pages/ui/modals.js') }}}"></script>
<script src="https://pondok-huda.com/js/validate.js" type="text/javascript"></script>
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
var hartot = 0;
var showdetail = false;
function load() {
    if ($("#form").valid()) {
        document.getElementById("loading").style.zIndex = "999";
        document.getElementById("loading").style.opacity = "0.8";
        showInputMessage();
    }
}
function uih(id) {
    var x = document.getElementById(id).value;
    if (x.charAt(0) == 0 && x.length != 1) {
        document.getElementById(id).value = "";  
    }
    var e = document.getElementById(id).value.replace(/[^0-9-.]/g, '');
    document.getElementById(id).value = e.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
    if (parseInt(document.getElementById(id).value.replace(/[^0-9-.]/g, '')) > parseInt(document.getElementById("hargatotal").value.replace(/[^0-9-.]/g, ''))) {
        var l = document.getElementById("hargatotal").value.replace(/[^0-9-.]/g, '');
        document.getElementById(id).value = l.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
    }
}
function detail() {
    if (showdetail == false) {
        document.getElementById("detail").style.display = "";
        document.getElementById("btndetail").className = "btn bg-red waves-effect";
        showdetail = true;
    }
    else {
        document.getElementById("detail").style.display = "none";
        document.getElementById("btndetail").className = "btn bg-{{Auth::user()->ownerkost[$indexKost]['kost']['name_color']}} waves-effect";
        showdetail = false;
    }
}
function isittl(id) {
    var x = document.getElementById(id).value;
    if (x.charAt(0) == 0 && x.length != 1) {
        document.getElementById(id).value = "";  
    }
    var e = document.getElementById(id).value.replace(/[^0-9-.]/g, '');
    document.getElementById(id).value = e.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
    if (parseInt(document.getElementById("periodebayar").value) < 1) {
        document.getElementById("periodebayar").value = 1;
    }
    if (document.getElementById("denda").value.replace(/[^0-9-.]/g, '') != null || document.getElementById("diskon").value.replace(/[^0-9-.]/g, '') != null) {
        var hargaperbulan = document.getElementById("hargaperbulan").value.replace(/[^0-9-.]/g, '');
        var periodebayar = document.getElementById("periodebayar").value.replace(/[^0-9-.]/g, '');
        var denda = document.getElementById("denda").value.replace(/[^0-9-.]/g, '');
        var diskon = document.getElementById("diskon").value.replace(/[^0-9-.]/g, '');
        var u = ((parseInt(hargaperbulan) * parseInt(periodebayar)) + parseInt(denda)) - parseInt(diskon);
        document.getElementById("hargatotal").value = u.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
    }

    if (parseInt(document.getElementById("diskon").value.replace(/[^0-9-.]/g, '')) < 0) {
        document.getElementById("diskon").value = "0";
        var hargaperbulan = document.getElementById("hargaperbulan").value.replace(/[^0-9-.]/g, '');
        var periodebayar = document.getElementById("periodebayar").value.replace(/[^0-9-.]/g, '');
        var denda = document.getElementById("denda").value.replace(/[^0-9-.]/g, '');
        var diskon = document.getElementById("diskon").value.replace(/[^0-9-.]/g, '');
        var u = ((parseInt(hargaperbulan) * parseInt(periodebayar)) + parseInt(denda)) - parseInt(diskon);
        document.getElementById("hargatotal").value = u.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
    }
    if (parseInt(document.getElementById("diskon").value.replace(/[^0-9-.]/g, '')) > parseInt(hartot)) {
        document.getElementById("diskon").value = hartot.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
        var hargaperbulan = document.getElementById("hargaperbulan").value.replace(/[^0-9-.]/g, '');
        var periodebayar = document.getElementById("periodebayar").value.replace(/[^0-9-.]/g, '');
        var denda = document.getElementById("denda").value.replace(/[^0-9-.]/g, '');
        var diskon = document.getElementById("diskon").value.replace(/[^0-9-.]/g, '');
        var u = ((parseInt(hargaperbulan) * parseInt(periodebayar)) + parseInt(denda)) - parseInt(diskon);
        document.getElementById("hargatotal").value = u.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
    }

    if (parseInt(document.getElementById("periodebayar").value.replace(/[^0-9-.]/g, '')) > 1) {
        document.getElementById("totalbayar").value = document.getElementById("hargatotal").value;
        document.getElementById("totalbayar").setAttribute('readonly', 'true');
    }
    else {
        document.getElementById("totalbayar").value = document.getElementById("hargatotal").value;
        document.getElementById("totalbayar").removeAttribute('readonly');
    }
}
function tfs() {
    document.getElementById("totalbayar").value = document.getElementById("hargatotal").value;
    var hargatotal = document.getElementById("hargatotal").value;
    var totalbayar = document.getElementById("totalbayar").value;

    document.getElementById("kembali").value = totalbayar - hargatotal;
}
function pulangan() {
    var hargatotal = document.getElementById("hargatotal").value;
    var totalbayar = document.getElementById("totalbayar").value;
    if (parseInt(hargatotal) < parseInt(totalbayar)) {
        document.getElementById("totalbayar").value = hargatotal;
    }
}
$(document).on("change", "#kode_kamar", function() {
    document.getElementById("loading").style.zIndex = "999";
    document.getElementById("loading").style.opacity = "0.8";
    var kodekamar = $(this).val();
    var nokamar = $("#kode_kamar option:selected").text();
    $.ajax({
        type: "post",
        url: "{{ route('owner.getdatasewa') }}",
        data: { _token: "{{csrf_token()}}", kodekamar : kodekamar, nokamar : nokamar},
        dataType: "json",
        success : function(data) {
            document.getElementById("detailPembayaran").style.display = "";
            document.getElementById("nyicil").style.display = "none";
            document.getElementById("baru").style.display = "none";

            document.getElementById("nokamar").value = nokamar;
            document.getElementById("kode_sewa").value = data['databayarsewa'][0]['kode_sewa'];
            document.getElementById("noktp").value = data['databayarsewa'][0]['noktp'];
            document.getElementById("periodesewa").value = data['databayarsewa'][0]['periodesewa'];
            if(data['databayarsewa'].length > 1)
            {
                document.getElementById("namapenyewa").value = data['databayarsewa'][0]['nama'] + ", " + data['databayarsewa'][1]['nama'];
            }
            else
            {
                document.getElementById("namapenyewa").value = data['databayarsewa'][0]['nama'];
            }
            document.getElementById("tglpembayaran").value = data['databayarsewa'][0]['tanggal_pembayaran'];
            document.getElementById("periodebayar").value = data['databayarsewa'][0]['periode_bayar'];
            document.getElementById("hargaperbulan").value = data['databayarsewa'][0]['harga'].toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
            document.getElementById("denda").value = data['databayarsewa'][0]['denda'].toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
            document.getElementById("diskon").value = "0";
            document.getElementById("denda_sebelumnya").value = data['databayarsewa'][0]['denda_sebelumnya'];
            document.getElementById("diskon_sebelumnya").value = data['databayarsewa'][0]['diskon_sebelumnya'];
            document.getElementById("harga_pindah_sebelumnya").value = data['databayarsewa'][0]['harga_pindah_sebelumnya'];
            document.getElementById("total_harga_sebelumnya").value = data['databayarsewa'][0]['total_harga_sebelumnya'];
            document.getElementById("total_bayar_sebelumnya").value = data['databayarsewa'][0]['total_bayar_sebelumnya'];
            document.getElementById("sisa_bayar").value = data['databayarsewa'][0]['sisa_bayar'];
            if (data['databayarsewa'][0]['sisa_bayar'] == 0 || data['databayarsewa'][0]['sisa_bayar'] == "0") {
                // if (document.getElementById("denda").value != "0" || document.getElementById("diskon").value != "0") {
                //     var hargaperbulan = document.getElementById("hargaperbulan").value;
                //     var periodebayar = document.getElementById("periodebayar").value;
                //     var denda = document.getElementById("denda").value;
                //     var diskon = document.getElementById("diskon").value;

                //     document.getElementById("hargatotal").value = ((parseInt(hargaperbulan) * parseInt(periodebayar)) + parseInt(denda)) - parseInt(diskon);
                // }
                var e = ((parseInt(data['databayarsewa'][0]['harga']) * parseInt(data['databayarsewa'][0]['periode_bayar'])) + parseInt(data['databayarsewa'][0]['denda'])) - parseInt(document.getElementById("diskon").value)
                document.getElementById("hargatotal").value = e.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
                document.getElementById("baru").style.display = "";
            }
            else {
                document.getElementById("hargatotal").value = data['databayarsewa'][0]['sisa_bayar'].toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
                document.getElementById("nyicil").style.display = "";
            }
            hartot = document.getElementById("hargatotal").value.replace(/[^0-9-.]/g, '');
            
            if (data['databayarsewa'][0]['periode_bayar'] > 1) {
                document.getElementById("totalbayar").value = document.getElementById("hargatotal").value.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
                document.getElementById("totalbayar").setAttribute('readonly', 'true');
            }
            else {
                document.getElementById("totalbayar").value = document.getElementById("hargatotal").value.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
                document.getElementById("totalbayar").removeAttribute('readonly');
            }
            document.getElementById("loading").style.zIndex = "-999";
            document.getElementById("loading").style.opacity = "0";
        },
        error : function(data) {
            $("#form input[type=text], #form textarea, #form input[type=password], #form input[type=number], #form input[type=email]").each(function(){
                var input = $(this); // This is the jquery object of the input, do what you will
                // console.log(input.attr('id'));
                // console.log(input.val());
                document.getElementById(input.attr('id')).value = "";
            });
            document.getElementById("loading").style.zIndex = "-999";
            document.getElementById("loading").style.opacity = "0";
        }
    });
});
$(document).ready(function() {
    document.getElementById("denda").value = 0;
    document.getElementById("diskon").value = 0;
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
});
</script>
@endsection
@include('layouts.footer')