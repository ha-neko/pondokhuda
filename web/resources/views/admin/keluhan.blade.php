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
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover js-basic-example dataTable">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Judul</th>
                                <th>Kategori</th>
                                <th>Nama</th>
                                <th>No. Kamar</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($keluhan['keluhan'] as $k)
                            @if (count($k['status'])==3)
                            <tr class="bg-grey">
                                <td>Selesai</td>
                                <td>{{ $k['tgl'] }}</td>
                                <td>{{ $k['judul'] }}</td>
                                <td>{{ $k['kategori'] }}</td>
                                <td>{{ $k['nama'] }}</td>
                                <td>{{ $k['nokamar'] }}</td>
                                <td>
                                    <a href="{{ route('admin.detailkeluhan', $k['kode']) }}"><button type="button" class="btn btn-warning waves-effect">Edit</button></a>
                                    <a href="javascript:void(0);" onclick="showcatmin({!! $k['kode'] !!})" data-toggle="modal" data-target="#largeModal"><button type="button" class="btn btn-primary waves-effect">Comment</button></a>
                                </td>
                            </tr>
                            @elseif (count($k['status'])==2)
                            <tr>
                                <td>Dikerjakan</td>
                                <td>{{ $k['tgl'] }}</td>
                                <td>{{ $k['judul'] }}</td>
                                <td>{{ $k['kategori'] }}</td>
                                <td>{{ $k['nama'] }}</td>
                                <td>{{ $k['nokamar'] }}</td>
                                <td>
                                    <a href="{{ route('admin.detailkeluhan', $k['kode']) }}"><button type="button" class="btn btn-warning waves-effect">Edit</button></a>
                                    <a href="javascript:void(0);" onclick="showcatmin({!! $k['kode'] !!})" data-toggle="modal" data-target="#largeModal"><button type="button" class="btn btn-primary waves-effect">Comment</button></a>
                                </td>
                            </tr>
                            @else
                            <tr>
                                <td>Pelaporan</td>
                                <td>{{ $k['tgl'] }}</td>
                                <td>{{ $k['judul'] }}</td>
                                <td>{{ $k['kategori'] }}</td>
                                <td>{{ $k['nama'] }}</td>
                                <td>{{ $k['nokamar'] }}</td>
                                <td>
                                    <a href="{{ route('admin.detailkeluhan', $k['kode']) }}"><button type="button" class="btn btn-warning waves-effect">Edit</button></a>
                                    <a href="javascript:void(0);" onclick="showcatmin({!! $k['kode'] !!})" data-toggle="modal" data-target="#largeModal"><button type="button" class="btn btn-primary waves-effect">Comment</button></a>
                                </td>
                            </tr>
                            @endif
                            @endforeach
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
<div class="modal fade" id="largeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog model-lg" role="document">
        <div class="modal-content modal-col-{{Auth::user()->adminkost[$indexKost]['kost']['name_color']}}">
            <div class="modal-header">
                <span class="right"><button type="button" data-dismiss="modal" class="form-control bg-{{Auth::user()->adminkost[$indexKost]['kost']['name_color']}} waves-effect" style="border-radius: 0px; border: none; box-shadow: none;"><i class="material-icons">close</i></button></span>
            </div>
            <div class="modal-body">
                <div class="row clearfix">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="header">
                                <h4>Catatan Keluhan</h4>
                            </div>
                            <div class="body">
                                <div class="form-group form-float">
                                    <label class="form-label">Komen</label>
                                    <div class="form-line">
                                        <input type="hidden" id="kodekeluhan" class="form-control">
                                        <textarea class="form-control" style="padding: 10px 10px; height: 140px; resize: none;" name="komen" id="komen" placeholder="Isi comment disini..."></textarea>
                                    </div>
                                    <br>
                                    <span class="right"><button type="button" class="btn bg-{{Auth::user()->adminkost[$indexKost]['kost']['name_color']}} waves-effect" onclick="tambahkomen()">POST NOTE</button></span>
                                    <br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="note">
                
                </div>
            </div>
        </div>
    </div>
</div>
<!-- #END# MODAL -->

@endsection
@section('js-content')
<script src="{{{ URL::asset('adminbsb/js/pages/ui/modals.js') }}}"></script>
<script type="text/javascript">
function showcatmin(kode) {
    document.getElementById("note").innerHTML = "";
    document.getElementById("kodekeluhan").value = kode.toString();
    $.ajax({
        type : "get",
        url  : "{{ route('admin.komenkeluhan') }}",
        dataType : "json",
        success :function(data){
            for (var i = 0; i < data['keluhan'].length; i++) {
                if (data['keluhan'][i]['kode'] == kode) {
                    if ( data['keluhan'][i]['komen'].length != 0) {
                        for (var j = 0; j < data['keluhan'][i]['komen'].length; j++) {
                            $("#note").append('<div class="row clearfix"><div class="card"><div class="header" style="word-break: break-all; border-bottom: 1px solid #77ff83">' + data["keluhan"][i]["komen"][j]["komen"] + '</div><div class="body" style="font-size:13px; color: grey; padding-bottom: 25px; padding-top: 13px"><span class="right">' + data["keluhan"][i]["komen"][j]["created"] + '</span></div></div>');
                        }
                    }
                    else {
                        $("#note").append("Belum ada catatan");
                    }
                }
            }
        },
        error : function(data){
            $("#note").append("ERROR saat mengambil catatan silahkan periksa koneksi terlebih dahulu atau refresh halaman");
        }
    });
}
function tambahkomen() {
    var kom = document.getElementById("komen").value;
    var kom_trim = kom.trim();
    if(kom_trim != "")
    {
        var nilai1 = document.getElementById("kodekeluhan").value;
        var nilai2 = document.getElementById("komen").value;
        $.ajax({
            type : "post",
            url  : "{{ route('admin.createkomenkeluhan') }}",
            data : { _token: "{{csrf_token()}}", nilai1 : nilai1, nilai2 : nilai2 },
            dataType : "json",
            success :function(data){
                if(data['keluhan'] == "komen berhasil ditambah") {
                    document.getElementById("komen").value = "";
                    document.getElementById("note").innerHTML = "";
                    var kode = document.getElementById("kodekeluhan").value;
                    $.ajax({
                        type : "get",
                        url  : "{{ route('admin.komenkeluhan') }}",
                        dataType : "json",
                        success :function(data){
                            for (var i = 0; i < data['keluhan'].length; i++) {
                                if (data['keluhan'][i]['kode'] == kode) {
                                    if ( data['keluhan'][i]['komen'].length != 0) {
                                        for (var j = 0; j < data['keluhan'][i]['komen'].length; j++) {
                                            $("#note").append('<div class="row clearfix"><div class="card"><div class="header" style="word-break: break-all; border-bottom: 1px solid #77ff83">' + data["keluhan"][i]["komen"][j]["komen"] + '</div><div class="body" style="font-size:13px; color: grey; padding-bottom: 25px; padding-top: 13px"><span class="right">' + data["keluhan"][i]["komen"][j]["created"] + '</span></div></div>');
                                        }
                                    }
                                    else {
                                        $("#note").append("Belum ada catatan");
                                    }
                                }
                            }
                        },
                        error : function(data){
                            $("#note").append("ERROR saat mengambil catatan silahkan periksa koneksi terlebih dahulu atau refresh halaman");
                        }
                    });
                }
                else {
                    alert("Gagal menambahkan komen silahkan cek koneksi kembali");
                }
            },
            error : function(data){
                alert("Gagal menambahkan komen silahkan cek koneksi kembali");
            }
        });
    }
    else
    {
        alert("Catatan masih kosong");
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
});
</script>
@endsection
@include('layouts.footer')