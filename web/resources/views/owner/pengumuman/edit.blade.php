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
                    DATA PENGUMUMAN
                </h2>
            </div>
            <div class="body">
                <div class="table-responsive">
                    @if($berita['pengumuman'] != "error")
                    @foreach($berita['pengumuman'] as $b)
                    <form action="{{ route('owner.updatepengumuman') }}" method="post">@csrf
                    @if ($b['kode'] == $kode)
                    <input type="hidden" name="kode" value="{{$kode}}">
                    <div class="col-sm-12">
                        <div class="form-group form-float">
                            <label class="form-label">Judul</label>
                            <div class="form-line">
                                <input type="text" id="judul" class="form-control" name="judul" placeholder="Judul" value="{{ $b['judul'] }}" readonly="true" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group form-float">
                            <label class="form-label">Berita</label>
                            <div class="form-line">
                                <input type="text" id="isip" class="form-control" name="berita" placeholder="Berita" value="{{ $b['berita'] }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group form-float">
                            <label class="form-label">Status Tampil</label>
                            <div class="form-line">
                                <select class="form-control" name="status" id="status">
                                    @if ($b['status'] == 'tampil')
                                    <option value="tampil" selected="selected">Tampil</option>
                                    <option value="tidak">Tidak Tampil</option>
                                    @else
                                    <option value="tampil">Tampil</option>
                                    <option value="tidak" selected="selected">Tidak Tampil</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="text-right">
                            <a href="{{ route('owner.pengumuman') }}">
                            <button class="btn bg-{{Auth::user()->ownerkost[$indexKost]['kost']['name_color']}} waves-effect" type="button"><i class="material-icons">keyboard_arrow_left</i> <span class="icon-name">Kembali</span></button>
                            </a>
                            <button class="btn bg-{{Auth::user()->ownerkost[$indexKost]['kost']['name_color']}} waves-effect" type="submit" onclick="load()"><i class="material-icons">present_to_all</i> <span class="icon-name">Update</span></button> 
                        </div>
                    </div>
                    @endif
                    @endforeach
                    @else
                    <h3>Tidak ada pengumuman</h3>
                    @endif
                    </form>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>
<!-- #END# TABLE -->
<div class="loading" id="loading">Loading&#8230;</div>
@endsection
@section('js-content')
<script src="{{{ URL::asset('adminbsb/js/pages/ui/modals.js') }}}"></script>
<script type="text/javascript">
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
});
function load() {
    if ($("#form").valid()) {
        document.getElementById("loading").style.zIndex = "999";
        document.getElementById("loading").style.opacity = "0.8";
        showInputMessage();
    }
}
</script>
@endsection
@include('layouts.footer')