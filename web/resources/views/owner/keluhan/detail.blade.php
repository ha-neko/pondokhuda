@include('layouts.header')
@extends('layouts.sidebar')
@extends('layouts.menubar')
@section('content')

<div class="block-header">
    <h2>{{ $data['pageTitle'] }}</h2>
</div>

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
                    <?php $i = 0; ?>
                    @foreach($keluhan['keluhan'] as $k)
                    @if ($k['kode'] == $kodes)
                    <?php $index = $i; ?>
                    <div class="form-group form-float">
                        <div class="form-line disabled">
                            <input type="text" class="form-control" name="noktp" value="{{ $k['judul'] }}" readonly="true" disabled="true">
                            <label class="form-label">Judul*</label>
                        </div>
                    </div>
                    <div class="form-group form-float">
                        <div class="form-line disabled">
                            <input type="text" class="form-control" name="noktp" value="{{ $k['kategori'] }}" readonly="true" disabled="true">
                            <label class="form-label">Kategori*</label>
                        </div>
                    </div>
                    <div class="form-group form-float">
                        <div class="form-line disabled">
                            <input type="text" class="form-control" name="noktp" value="{{ $k['tgl'] }}" readonly="true" disabled="true">
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
                        <div class="form-line disabled">
                            <div id="aniimated-thumbnials" class="list-unstyled row clearfix">
                                @foreach ($k['foto'] as $f)
                                <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                                    <a href="https://pondok-huda.com{{ $f['link'] }}" data-sub-html="{{ $k['judul'] }}">
                                        <img class="img-responsive thumbnail" src="https://pondok-huda.com{{ $f['link'] }}">
                                    </a>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="demo-checkbox">
                        <b>Proses</b><br><br>
                        @php
                        $status = array("Pelaporan", "Dikerjakan", "Selesai")
                        @endphp
                        @foreach ($k['status'] as $s)
                            @if (in_array("Pelaporan", $s))
                            <input type="checkbox" id="checkbox_pelaporan" name="checkbox_pelaporan" class="filled-in chk-col-{{Auth::user()->ownerkost[$indexKost]['kost']['name_color']}}" disabled="" />
                            <label for="checkbox_pelaporan" id="lbl_pelaporan">Pelaporan</label><br>
                            @if (in_array("Pelaporan", $status))
                                @php
                                    unset($status[0])
                                @endphp
                            @endif
                            @endif
                            @if (in_array("Dikerjakan", $s))
                            <input type="checkbox" id="checkbox_dikerjakan" name="checkbox_dikerjakan" class="filled-in chk-col-{{Auth::user()->ownerkost[$indexKost]['kost']['name_color']}}" disabled="" />
                            <label for="checkbox_dikerjakan" id="lbl_dikerjakan">Dikerjakan</label><br>
                            @if (in_array("Dikerjakan", $status))
                                @php
                                    unset($status[1])
                                @endphp
                            @endif
                            @endif
                            @if (in_array("Selesai", $s))
                            <input type="checkbox" id="checkbox_selesai" name="checkbox_selesai" class="filled-in chk-col-{{Auth::user()->ownerkost[$indexKost]['kost']['name_color']}}" disabled="" />
                            <label for="checkbox_selesai" id="lbl_selesai">Selesai</label><br>
                            @if (in_array("Selesai", $status))
                                @php
                                    unset($status[2])
                                @endphp
                            @endif
                            @endif
                        @endforeach
                        @if (in_array("Pelaporan", $status))
                        <input type="checkbox" id="checkbox_pelaporan" name="checkbox_pelaporan" class="filled-in chk-col-{{Auth::user()->ownerkost[$indexKost]['kost']['name_color']}}" disabled="" />
                        <label for="checkbox_pelaporan" id="lbl_pelaporan">Pelaporan</label><br>
                        @endif
                        @if (in_array("Dikerjakan", $status))
                        <input type="checkbox" id="checkbox_dikerjakan" name="checkbox_dikerjakan" class="filled-in chk-col-{{Auth::user()->ownerkost[$indexKost]['kost']['name_color']}}" disabled="" />
                        <label for="checkbox_dikerjakan" id="lbl_dikerjakan">Dikerjakan</label><br>
                        @endif
                        @if (in_array("Selesai", $status))
                        <input type="checkbox" id="checkbox_selesai" name="checkbox_selesai" class="filled-in chk-col-{{Auth::user()->ownerkost[$indexKost]['kost']['name_color']}}" disabled="" />
                        <label for="checkbox_selesai" id="lbl_selesai">Selesai</label><br>
                        @endif
                    @endif
                    <?php $i++; ?>
                    @endforeach
                </fieldset>
                <div class="text-right">
                    <a href="{{ route('owner.keluhan') }}">
                        <button class="btn waves-effect bg-{{Auth::user()->ownerkost[$indexKost]['kost']['name_color']}}" type="button">
                            <i class="material-icons">keyboard_arrow_left</i>
                            <span class="icon-name">Kembali</span>
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('js-content')
<script type="text/javascript">
$(document).ready(function() {
    var status = "{!! count($keluhan['keluhan'][$index]['status']) !!}"
    if (status==1)
    {
        $("input[name=checkbox_pelaporan]").prop('checked',true);
        $('#lbl_pelaporan').append(" : {!! $keluhan['keluhan'][$index]['status'][0]['tgl'] !!}");
    }
    else if (status==2)
    {
        $("input[name=checkbox_pelaporan]").prop('checked',true);
        $('#lbl_pelaporan').append(" : {!! $keluhan['keluhan'][$index]['status'][0]['tgl'] !!}");

        $("input[name=checkbox_dikerjakan]").prop('checked',true);
        $('#lbl_dikerjakan').append(" : {!! isset($keluhan['keluhan'][$index]['status'][1]['tgl']) == 1 ? $keluhan['keluhan'][$index]['status'][1]['tgl'] : '' !!}");
    }
    else if (status==3)
    {
        $("input[name=checkbox_pelaporan]").prop('checked',true);
        $('#lbl_pelaporan').append(" : {!! $keluhan['keluhan'][$index]['status'][0]['tgl'] !!}");

        $("input[name=checkbox_dikerjakan]").prop('checked',true);
        $('#lbl_dikerjakan').append(" : {!! isset($keluhan['keluhan'][$index]['status'][1]['tgl']) == 1 ? $keluhan['keluhan'][$index]['status'][1]['tgl'] : '' !!}");

        $("input[name=checkbox_selesai]").prop('checked',true);
        $('#lbl_selesai').append(" : {!! isset($keluhan['keluhan'][$index]['status'][2]['tgl'])  == 1 ? $keluhan['keluhan'][$index]['status'][2]['tgl'] : '' !!}");
    }
});
</script>
<!-- Light Gallery Plugin Js -->
<script src="{{{ URL::asset('adminbsb/plugins/light-gallery/js/lightgallery-all.js') }}}"></script>

<!-- Custom Js -->
<script src="{{{ URL::asset('adminbsb/js/pages/medias/image-gallery.js') }}}"></script>
@endsection
@include('layouts.footer')