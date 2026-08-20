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
                    DATA KELUHAN
                </h2>
            </div>
            <div class="body">
                <fieldset>
                    @if($keluhan['keluhan'] != null)
                    @foreach($keluhan['keluhan'] as $k)
                    @if ($k['kode'] == $kodes)
                    <div class="form-group form-float">
                        <div class="form-line disabled">
                            <input type="text" class="form-control" name="judul" value="{{ $k['judul'] }}" readonly="true" disabled="true">
                            <label class="form-label">Judul*</label>
                        </div>
                    </div>
                    <div class="form-group form-float">
                        <div class="form-line disabled">
                            <input type="text" class="form-control" name="kategori" value="{{ $k['kategori'] }}" readonly="true" disabled="true">
                            <label class="form-label">Kategori*</label>
                        </div>
                    </div>
                    <div class="form-group form-float">
                        <div class="form-line disabled">
                            <input type="text" class="form-control" name="tanggal" value="{{ $k['tgl'] }}" readonly="true" disabled="true">
                            <label class="form-label">Tanggal*</label>
                        </div>
                    </div>
                    <div class="form-group form-float">
                        <div class="form-line disabled">
                            <textarea class="form-control" readonly="true" disabled="true">{{ $k['uraian'] }}</textarea>
                            <label class="form-label">Uraian*</label>
                        </div>
                    </div>
                    <div class="form-group form-float">
                        <div class="">
                            <div id="aniimated-thumbnials" class="list-unstyled row clearfix">
                                @foreach ($k['foto'] as $f)
                                <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                                    <a href="https://pondok-huda.com{{ $f['link'] }}" data-sub-html="{{ $k['judul'] }}">
                                        <img class="" src="https://pondok-huda.com{{ $f['link'] }}" style="width: 200px" alt="No Image">
                                    </a>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <form id="form" action="{{ route('admin.updatekeluhan', $kodes) }}" method="post">
                        @csrf
                    <div class="form-group form-float">
                        <div class="form-line">
                            <div class="demo-checkbox">
                                <b>Proses</b><br><br>
                                @php
                                $status = array("Pelaporan", "Dikerjakan", "Selesai")
                                @endphp
                                @foreach ($k['status'] as $s)
                                    @if (in_array("Pelaporan", $s))
                                    <input type="checkbox" id="md_checkbox_31" class="filled-in chk-col-{{Auth::user()->adminkost[$indexKost]['kost']['name_color']}}" disabled="" name="pelaporan" checked />
                                    <label for="md_checkbox_31">Pelaporan : {{ $s['tgl'] }}</label><br>
                                    @if (in_array("Pelaporan", $status))
                                        @php
                                            unset($status[0])
                                        @endphp
                                    @endif
                                    @endif
                                    @if (in_array("Dikerjakan", $s))
                                    <input type="checkbox" id="md_checkbox_32" class="filled-in chk-col-{{Auth::user()->adminkost[$indexKost]['kost']['name_color']}}" disabled="" name="dikerjakan" checked />
                                    <label for="md_checkbox_32">Dikerjakan : {{ $s['tgl'] }}</label><br>
                                    @if (in_array("Dikerjakan", $status))
                                        @php
                                            unset($status[1])
                                        @endphp
                                    @endif
                                    @endif
                                    @if (in_array("Selesai", $s))
                                    <input type="checkbox" id="md_checkbox_33" class="filled-in chk-col-{{Auth::user()->adminkost[$indexKost]['kost']['name_color']}}" disabled="" name="selesai" checked />
                                    <label for="md_checkbox_33">Selesai : {{ $s['tgl'] }}</label><br>
                                    @if (in_array("Selesai", $status))
                                        @php
                                            unset($status[2])
                                        @endphp
                                    @endif
                                    @endif
                                @endforeach
                                <!-- @foreach ($status as $st)
                                {{ $st }}<br>
                                @endforeach -->
                                @if (in_array("Pelaporan", $status))
                                <input type="checkbox" id="md_checkbox_31" class="filled-in chk-col-{{Auth::user()->adminkost[$indexKost]['kost']['name_color']}}" name="pelaporan" required />
                                <label for="md_checkbox_31">Pelaporan</label><br>
                                @endif
                                @if (in_array("Dikerjakan", $status))
                                <input type="checkbox" id="md_checkbox_32" class="filled-in chk-col-{{Auth::user()->adminkost[$indexKost]['kost']['name_color']}}" name="dikerjakan" value="Dikerjakan" required />
                                <label for="md_checkbox_32">Dikerjakan</label><br>
                                @endif
                                @if (in_array("Selesai", $status))
                                <input type="checkbox" id="md_checkbox_33" class="filled-in chk-col-{{Auth::user()->adminkost[$indexKost]['kost']['name_color']}}" name="selesai" value="Selesai" disabled="true" />
                                <label for="md_checkbox_33">Selesai</label><br>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @endforeach
                    <div class="text-right">
                            <a href="{{ route('admin.keluhan') }}">
                            <button class="btn bg-{{Auth::user()->adminkost[$indexKost]['kost']['name_color']}} waves-effect" type="button"><i class="material-icons">keyboard_arrow_left</i> <span class="icon-name">Kembali</span></button>
                            </a>
                            <button id="btnUpdate" class="btn bg-{{Auth::user()->adminkost[$indexKost]['kost']['name_color']}} waves-effect" type="submit" onclick="load()"><i class="material-icons">present_to_all</i> <span class="icon-name">Update</span></button>
                    </div>
                    </form>
                    @else
                    <h3>Tidak ada keluhan</h3>
                    @endif
                    
                        <div class="col-xs-6 align-right">
                            <!-- <a href="#">
                            <button class="btn bg-{{Auth::user()->adminkost[$indexKost]['kost']['name_color']}} waves-effect" type="button"><i class="material-icons">chat</i> <span class="icon-name">Chat</span></button>
                            </a> -->
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
</div>
<div class="loading" id="loading">Loading&#8230;</div>
@endsection
@section('js-content')
<script src="{{{ URL::asset('adminbsb/js/pages/ui/modals.js') }}}"></script>
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
$(document).ready(function() {
    if (document.getElementById("md_checkbox_31").checked == true && document.getElementById("md_checkbox_32").checked == false) {
        document.getElementById("md_checkbox_33").id = "md_checkbox_33";
    }
    if (document.getElementById("md_checkbox_32").checked == true) {
        document.getElementById("md_checkbox_33").removeAttribute("disabled");
        document.getElementById("md_checkbox_33").setAttribute("required", "true");
    }
    if (document.getElementById("md_checkbox_33").checked == true) {
        document.getElementById("btnUpdate").style.display = "none";
        document.getElementById("md_checkbox_33").disabled = true;
        // document.getElementById("uus").removeAttribute("action");
        // document.getElementById("uus").removeAttribute("method");
    }
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
function load() {
    if ($("#form").valid()) {
        document.getElementById("loading").style.zIndex = "999";
        document.getElementById("loading").style.opacity = "0.8";
        showInputMessage(); 
    }
}
</script>
<!-- Light Gallery Plugin Js -->
<script src="{{{ URL::asset('adminbsb/plugins/light-gallery/js/lightgallery-all.js') }}}"></script>

<!-- Custom Js -->
<script src="{{{ URL::asset('adminbsb/js/pages/medias/image-gallery.js') }}}"></script>
@endsection
@include('layouts.footer')