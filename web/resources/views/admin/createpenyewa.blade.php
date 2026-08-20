@include('layouts.header')
@extends('layouts.sidebar')
@extends('layouts.menubar')
@section('content')

<form action="{{route('admin.createpenyewa')}}" method="POST">
    @csrf
<input type="hidden" name="kode">
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>FORM PENGISIAN</h2>
                </div>
                <div class="body">
                    <h3>Informasi Umum</h3>
                    <fieldset>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" class="form-control" name="noktp" required>
                                <label class="form-label">No. KTP*</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" class="form-control" name="nama" required>
                                <label class="form-label">Nama*</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line" >
                                <input type="text" id='datePicker' class="form-control" name="tgllhr">
                                <label class="form-label">Tanggal lahir*</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" class="form-control" name="nohp" required>
                                <label class="form-label">No. HP*</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-group">
                                <input type="radio" name="gender" id="male" class="with-gap" value="Pria">
                                <label for="male">Laki - laki</label>

                                <input type="radio" name="gender" id="female" class="with-gap" value="Wanita">
                                <label for="female" class="m-l-20">Perempuan</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="email" class="form-control" name="email" required>
                                <label class="form-label">Email*</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" class="form-control" name="namaortu" required>
                                <label class="form-label">Nama Orang tua*</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" class="form-control" name="nohportu" required>
                                <label class="form-label">No. HP Orang tua*</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" class="form-control" name="tempatkuliahkerja" required>
                                <label class="form-label">Lokasi Kuliah/Kerja*</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" class="form-control" name="jurkuliah">
                                <label class="form-label">Jurusan Kuliah*</label>
                            </div>
                        </div>
                    </fieldset>

                    <h3>Informasi Domisili</h3>
                    <fieldset>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" name="alamat" class="form-control" required>
                                <label class="form-label">Alamat*</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                @php
                                $p = "";
                                @endphp
                                <select class="form-control show-tick" name="provinsi" id="cboxProv">
                                    <option value="">-- Provinsi --</option>
                                    @foreach($penyewa['masterprovince'] as $k)
                                    <option value="{{ $k['provinsi'] }}">{{ $k['provinsi'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <select class="form-control show-tick" name="kotakab" id="cboxKotakab">
                                    <option value="">-- Kota/Kabupaten --</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" name="kecamatan" class="form-control" required>
                                <label class="form-label">Kecamatan*</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" name="kelurahan" class="form-control" required>
                                <label class="form-label">Kelurahan*</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" name="kodepos" class="form-control" required>
                                <label class="form-label">Kode Pos*</label>
                            </div>
                        </div>
                    </fieldset>

                    <h3>Informasi Sewa Kos</h3>
                    <fieldset>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <select class="form-control show-tick" name="nokamar">
                                    <option value="">-- Pilih No. Kamar --</option>
                                    @foreach($penyewa['kamartersedia'] as $k)
                                    <option value="{{ $k }}">{{ $k }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" name="periodebayar" class="form-control" required>
                                <label class="form-label">Periode Bayar*</label>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
<button type="submit" class="btn btn-link waves-effect">SAVE</button>
<button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
</form>

@endsection
@section('js-content')
<script src="{{{ URL::asset('adminbsb/js/pages/ui/modals.js') }}}"></script>
<script type="text/javascript">
// window.onload = function() {
//     document.getElementById("cboxProv").selectedIndex = 0;
// }

$(document).on("change","#cboxProv",function(){
    var nilai = $(this).val();
    $.ajax({
        type : "post",
        url  : "{{ route('admin.kotakab') }}",
        data : { _token: "{{csrf_token()}}", nilai : nilai},
        dataType : "json",
        success :function(data){
            var i;
            for (i = 0; i < data['getdatakotakab'].length; i++) {

                // var option = document.createElement("option");
                // option.text = data['getdatakotakab'][i]['kotakab'];
                // option.value = data['getdatakotakab'][i]['kotakab'];
                // var select = document.getElementById("cboxProv");

                // select.append(option);

                // var node = document.createElement("option");                 // Create a <li> node
                // var textnode = document.createTextNode(data['getdatakotakab'][i]['kotakab']);
                // node.appendChild(textnode);
                // document.getElementById("cboxKotakab").appendChild(node);
                
                var x = document.getElementById("cboxKotakab");
                var option = document.createElement("option");
                option.text = data['getdatakotakab'][i]['kotakab'];
                option.value = data['getdatakotakab'][i]['kotakab'];
                x.add(option);
            }
        },
        error : function(data){

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
@endsection
@include('layouts.footer')