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
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="body">
        						<form onSubmit="return validate()" id="form" action="{{ route('owner.pendapatan-lain-lain') }}" method="post">
        						@csrf
                                    <input type="hidden" name="textpengeluaran" id="tp">
                                    <div id="showKeterangan">
                                    <div class="form-group form-float">
                                        <label for="totalbayar">Keterangan</label>
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="keterangan" id="keterangan" placeholder="Keterangan" required>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <label for="totalbayar">Total Pendapatan</label>
                                        <div class="form-line">
                                            <input type="text" class="form-control" oninput="ttlbyr(this.value)" value="" name="totalbayar" id="totalbayar" placeholder="Total Pendapatan" required maxlength="19">
                                            <input type="hidden" name="ttl" id="ttl" placeholder="Total Pendapatan">
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <label class="form-label">Metode</label>
                                        <div class="form-line" >
                                            <div class="form-group">
                                                <input type="radio" name="metode" id="tunai" class="with-gap" value="Tunai" checked="checked" required>
                                                <label for="tunai">Tunai</label>

                                                <input type="radio" name="metode" id="transfer" class="with-gap" value="Transfer" required>
                                                <label for="transfer" class="m-l-20">Transfer</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <button class="btn bg-{{Auth::user()->ownerkost[$indexKost]['kost']['name_color']}} waves-effect" type="submit" onclick="load()"><i class="material-icons">done</i> <span class="icon-name">Simpan</span></button>
                                    </div>
                                </form>
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
function load() {
    if ($("#form").valid()) {
        document.getElementById("loading").style.zIndex = "999";
        document.getElementById("loading").style.opacity = "0.8";
        showInputMessage();
    }
}
function ttlbyr(num) {
    if (document.getElementById("totalbayar").value == "NaN") {
        document.getElementById("totalbayar").value = "";
        document.getElementById("ttl").value = "";
    }
    priceVal = parseFloat(num.replace(/[^0-9-.]/g, ''));
    document.getElementById("ttl").value = priceVal;
    ttlVal = document.getElementById("ttl").value;
    var c = ttlVal.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
    document.getElementById("totalbayar").value = c;
    if (document.getElementById("totalbayar").value == 'NaN') {
        document.getElementById("totalbayar").value = "";
        document.getElementById("ttl").value = "";
    }
}
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
<!-- Bootstrap Material Datetime Picker Plugin Js -->
<script src="{{{ URL::asset('adminbsb/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}}"></script>
<!-- Dropzone Plugin Js -->
<script src="{{{ URL::asset('adminbsb/plugins/dropzone/dropzone.js') }}}"></script>

<!-- Bootstrap Colorpicker Js -->
<script src="{{{ URL::asset('adminbsb/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js') }}}"></script>

<script src="{{{ URL::asset('adminbsb/js/pages/forms/advanced-form-elements.js') }}}"></script>
@endsection
@include('layouts.footer')