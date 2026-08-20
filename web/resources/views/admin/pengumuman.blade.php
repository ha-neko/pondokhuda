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
                    DATA PENGUMUMAN
                </h2>
                <ul class="header-dropdown m-r--5">
                    <li class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons">more_vert</i>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <li data-toggle="modal" data-target="#largeModal">
                                <a href="javascript:void(0);">
                                    <i class="material-icons">add</i> <span class="icon-name">Buat Baru</span>
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
                                <th>Tgl Publish</th>
                                <th>Judul</th>
                                <th>Pengumuman</th>
                                <th>Last Update</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($berita['pengumuman'] != 'error')
                            @foreach($berita['pengumuman'] as $news)
                            @if ($news['status'] == 'tidak')
                            <tr class="bg-grey">
                                <td>{{ $news['tglpublish'] }}</td>
                                <td>{{ $news['judul'] }}</td>
                                <td>{{ $news['berita'] }}</td>
                                <td>{{ $news['lastupdate'] }}</td>
                                <td>{{ $news['status'] }}</td>
                                <td>
                                    <a href="{{route('admin.detailpengumuman', $news['kode'])}}">
                                        <button type="button" class="btn btn-warning waves-effect">Edit</button>
                                    </a>
                                </td>
                            </tr>
                            @else
                            <tr>
                                <td>{{ $news['tglpublish'] }}</td>
                                <td>{{ $news['judul'] }}</td>
                                <td>{{ $news['berita'] }}</td>
                                <td>{{ $news['lastupdate'] }}</td>
                                <td>{{ $news['status'] }}</td>
                                <td>
                                    <a href="{{route('admin.detailpengumuman', $news['kode'])}}">
                                        <button type="button" class="btn btn-warning waves-effect">Edit</button>
                                    </a>
                                </td>
                            </tr>
                            @endif
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
<div class="modal fade" id="largeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-col-{{Auth::user()->adminkost[$indexKost]['kost']['name_color']}}">
            <div class="modal-header">
                <h4 class="modal-title" id="largeModalLabel">INPUT PENGUMUMAN BARU</h4>
            </div>
            <form onSubmit="return validate()" id="form" action="{{route('admin.createpengumuman')}}" method="POST" enctype="multipart/form-data">
                @csrf
            <div class="modal-body">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header">
                                <h2>FORM PENGISIAN</h2>
                            </div>
                            <div class="body">
                                <fieldset>
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="text" id="judul" class="form-control" name="judul" required>
                                            <label class="form-label">Judul Pengumuman*</label>
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <textarea rows="4" id="isip" class="form-control no-resize" name="isi" required></textarea>
                                            <label class="form-label">Isi Pengumuman*</label>
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <select class="form-control show-tick" id="status" name="status" required>
                                                <option value="">-- Status --</option>
                                                <option value="tampil">Tampil</option>
                                                <option value="tidak">Tidak Tampil</option>
                                            </select>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-link waves-effect" onclick="load()">SAVE</button>
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- #END# TABLE -->
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