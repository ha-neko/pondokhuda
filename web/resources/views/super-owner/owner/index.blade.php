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
        <div class="card">
            <div class="header">
                <h2>
                    Data Owner
                </h2>
                <ul class="header-dropdown m-r--5">
                    <li class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons">more_vert</i>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <li>
                                <a href="{{ route('super-owner.owner-add') }}">
                                    <i class="material-icons">person_add</i>
                                    Tambah Owner
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="body table-responsive">
                <table class="table table-bordered table-striped table-hover js-basic-example dataTable">
                    <thead>
                        <tr>
                            <th>Nama Owner</th>
                            <th>Nomor Telepon</th>
                            <th>Email</th>
                            <th>Tanggal Daftar</th>
                            <th>Kost Yang Dimiliki</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $kost = "";
                        $index = 0;
                        @endphp
                        @if($owners['owner'] != null)
                            @for($i = 0; $i < count($owners['owner']); ++$i)
                                @if($i == count($owners['owner']) - 1)
                                    @if(count($owners['owner']) == 1)
                                    <tr>
                                        <td>{{ $owners['owner'][$i]['nama'] }}</td>
                                        <td>{{ $owners['owner'][$i]['email'] }}</td>
                                        <td>{{ $owners['owner'][$i]['nomortelepon'] }}</td>
                                        <td>{{ $owners['owner'][$i]['tgldaftar'] }}</td>
                                        <td>{{ $owners['owner'][$i]['namakost'] }}</td>
                                        <td>
                                            <a href="javascript:void(0);" onclick="getDetailOwner('{{ $owners["owner"][$i]["kode"] }}');" data-toggle="modal" data-target="#detailOwner" class="btn btn-primary">
                                                Detail
                                            </a>
                                            <a href="{{ route('super-owner.owner-edit', $owners['owner'][$i]['kode']) }}" class="btn btn-warning">
                                                Ubah
                                            </a>
                                            <a href="javascript:void(0);" onclick="konfirmasiHapus('{{$owners["owner"][$i]["kode"]}}')" class="btn btn-danger">
                                                Hapus
                                            </a>
                                        </td>
                                    </tr>
                                    @else
                                        @if($owners['owner'][$i]['kode'] == $owners['owner'][$i - 1]['kode'])
                                            @php

                                            if($index == 0)
                                                $kost = $kost.$owners['owner'][$i]['namakost'];
                                            else
                                                $kost = $kost.", ".$owners['owner'][$i]['namakost'];

                                            $index++;

                                            @endphp
                                            <tr>
                                                <td>{{ $owners['owner'][$i]['nama'] }}</td>
                                                <td>{{ $owners['owner'][$i]['nomortelepon'] }}</td>
                                                <td>{{ $owners['owner'][$i]['email'] }}</td>
                                                <td>{{ $owners['owner'][$i]['tgldaftar'] }}</td>
                                                <td>{{ $kost }}</td>
                                                <td>
                                                    <a href="javascript:void(0);" onclick="getDetailOwner('{{ $owners["owner"][$i]["kode"] }}');" data-toggle="modal" data-target="#detailOwner" class="btn btn-primary">
                                                        Detail
                                                    </a>
                                                    <a href="{{ route('super-owner.owner-edit', $owners['owner'][$i]['kode']) }}" class="btn btn-warning">
                                                        Ubah
                                                    </a>
                                                    <a href="javascript:void(0);" onclick="konfirmasiHapus('{{$owners["owner"][$i]["kode"]}}')" class="btn btn-danger">
                                                        Hapus
                                                    </a>
                                                </td>
                                            </tr>
                                            @php $kost = ""; $index = 0; @endphp
                                        @else
                                        <tr>
                                            <td>{{ $owners['owner'][$i]['nama'] }}</td>
                                            <td>{{ $owners['owner'][$i]['nomortelepon'] }}</td>
                                            <td>{{ $owners['owner'][$i]['email'] }}</td>
                                            <td>{{ $owners['owner'][$i]['tgldaftar'] }}</td>
                                            <td>{{ $owners['owner'][$i]['namakost'] }}</td>
                                            <td>
                                                <a href="javascript:void(0);" onclick="getDetailOwner('{{ $owners["owner"][$i]["kode"] }}');" data-toggle="modal" data-target="#detailOwner" class="btn btn-primary">
                                                    Detail
                                                </a>
                                                <a href="{{ route('super-owner.owner-edit', $owners['owner'][$i]['kode']) }}" class="btn btn-warning">
                                                    Ubah
                                                </a>
                                                <a href="javascript:void(0);" onclick="konfirmasiHapus('{{$owners["owner"][$i]["kode"]}}')" class="btn btn-danger">
                                                    Hapus
                                                </a>
                                            </td>
                                        </tr>
                                        @endif
                                    @endif
                                @else
                                    @if($owners['owner'][$i]['kode'] == $owners['owner'][$i + 1]['kode'])
                                        @php

                                        if($index == 0)
                                            $kost = $kost.$owners['owner'][$i]['namakost'];
                                        else
                                            $kost = $kost.", ".$owners['owner'][$i]['namakost'];

                                        $index++;

                                        @endphp
                                    @else
                                        @if($i > 0)
                                            @if($owners['owner'][$i]['kode'] == $owners['owner'][$i - 1]['kode'])
                                                @php

                                                if($index == 0)
                                                    $kost = $kost.$owners['owner'][$i]['namakost'];
                                                else
                                                    $kost = $kost.", ".$owners['owner'][$i]['namakost'];

                                                $index++;

                                                @endphp
                                                <tr>
                                                    <td>{{ $owners['owner'][$i]['nama'] }}</td>
                                                    <td>{{ $owners['owner'][$i]['nomortelepon'] }}</td>
                                                    <td>{{ $owners['owner'][$i]['email'] }}</td>
                                                    <td>{{ $owners['owner'][$i]['tgldaftar'] }}</td>
                                                    <td>{{ $kost }}</td>
                                                    <td>
                                                        <a href="javascript:void(0);" onclick="getDetailOwner('{{ $owners["owner"][$i]["kode"] }}');" data-toggle="modal" data-target="#detailOwner" class="btn btn-primary">
                                                            Detail
                                                        </a>
                                                        <a href="{{ route('super-owner.owner-edit', $owners['owner'][$i]['kode']) }}" class="btn btn-warning">
                                                            Ubah
                                                        </a>
                                                        <a href="javascript:void(0);" onclick="konfirmasiHapus('{{$owners["owner"][$i]["kode"]}}')" class="btn btn-danger">
                                                            Hapus
                                                        </a>
                                                    </td>
                                                </tr>
                                                @php $kost = ""; $index = 0; @endphp
                                            @else
                                            <tr>
                                                <td>{{ $owners['owner'][$i]['nama'] }}</td>
                                                <td>{{ $owners['owner'][$i]['nomortelepon'] }}</td>
                                                <td>{{ $owners['owner'][$i]['email'] }}</td>
                                                <td>{{ $owners['owner'][$i]['tgldaftar'] }}</td>
                                                <td>{{ $owners['owner'][$i]['namakost'] }}</td>
                                                <td>
                                                    <a href="javascript:void(0);" onclick="getDetailOwner('{{ $owners["owner"][$i]["kode"] }}');" data-toggle="modal" data-target="#detailOwner" class="btn btn-primary">
                                                        Detail
                                                    </a>
                                                    <a href="{{ route('super-owner.owner-edit', $owners['owner'][$i]['kode']) }}" class="btn btn-warning">
                                                        Ubah
                                                    </a>
                                                    <a href="javascript:void(0);" onclick="konfirmasiHapus('{{$owners["owner"][$i]["kode"]}}')" class="btn btn-danger">
                                                        Hapus
                                                    </a>
                                                </td>
                                            </tr>
                                            @endif
                                        @else
                                        <tr>
                                            <td>{{ $owners['owner'][$i]['nama'] }}</td>
                                            <td>{{ $owners['owner'][$i]['nomortelepon'] }}</td>
                                            <td>{{ $owners['owner'][$i]['email'] }}</td>
                                            <td>{{ $owners['owner'][$i]['tgldaftar'] }}</td>
                                            <td>{{ $owners['owner'][$i]['namakost'] }}</td>
                                            <td>
                                                <a href="javascript:void(0);" onclick="getDetailOwner('{{ $owners["owner"][$i]["kode"] }}');" data-toggle="modal" data-target="#detailOwner" class="btn btn-primary">
                                                    Detail
                                                </a>
                                                <a href="{{ route('super-owner.owner-edit', $owners['owner'][$i]['kode']) }}" class="btn btn-warning">
                                                    Ubah
                                                </a>
                                                <a href="javascript:void(0);" onclick="konfirmasiHapus('{{$owners["owner"][$i]["kode"]}}')" class="btn btn-danger">
                                                    Hapus
                                                </a>
                                            </td>
                                        </tr>
                                        @endif
                                    @endif
                                @endif
                            @endfor
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DETAIL -->
<div class="modal fade" id="detailOwner" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="largeModalLabel">DETAIL DATA OWNER</h4>
            </div>
            <div class="modal-body">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <center>
                            <div class="form-group form-float">
                                <div id="img-foto-detail" style="background-image: url(''); width: 150px; height: 150px; background-size: cover; background-repeat: no-repeat; background-position: 50% 50%; border-radius: 100px; border: 5px solid #d4d4d6;"></div>
                            </div>
                        </center>
                        <div class="form-group form-float">
                            <label class="form-label">Nama Owner</label>
                            <div class="form-line">
                                <input type="text" class="form-control" id="nama-detail" name="nama-detail" readonly="true">
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <label class="form-label">Nomor Telepon</label>
                            <div class="form-line">
                                <input type="text" class="form-control" id="notelp-detail" name="notelp-detail" readonly="true">
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <label class="form-label">Email</label>
                            <div class="form-line">
                                <input type="email" class="form-control" id="email-detail" name="email-detail" readonly="true">
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <label class="form-label">Tanggal Daftar</label>
                            <div class="form-line">
                                <input type="text" class="form-control" id="tgldaftar-detail" name="tgldaftar-detail" readonly="true">
                            </div>
                        </div>
                        <label class="form-label">Kost Yang Dimiliki</label>
                        <div class="table-responsive">
                            <table id='dataTable' class='table table-bordered table-striped table-hover'>
                                <thead>
                                    <tr>
                                        <th>Nama Kost</th>
                                        <th>Alamat</th>
                                        <th>Email Kost</th>
                                    </tr>
                                </thead>
                                <tbody id="tabel-detail-kost">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>
<!-- #END# MODAL DETAIL -->
@endsection

@section('js-content')

<script type="text/javascript">

    $(document).ready(function() {
        $('.js-basic-example').DataTable({
            responsive: true
        });
    });

    function getDetailOwner(kode) {
        $.ajax({
            type : "post",
            url  : "{{ route('super-owner.owner-detail') }}",
            data : { _token: "{{csrf_token()}}", kode : kode },
            dataType : "json",
            success :function(data){
                document.getElementById("img-foto-detail").style.backgroundImage = "url('" + data['owner'][0]['urlfoto'] + "')";
                document.getElementById("nama-detail").value = data['owner'][0]['nama'];
                document.getElementById("notelp-detail").value = data['owner'][0]['nomortelepon'];
                document.getElementById("email-detail").value = data['owner'][0]['email'];
                document.getElementById("tgldaftar-detail").value = data['owner'][0]['tgldaftar'];

                document.getElementById("tabel-detail-kost").innerHTML = ""

                for (var i = 0; i < data['owner'].length; i++)
                {
                    if(data['owner'][0]['namakost'] != null)
                    {
                        document.getElementById("tabel-detail-kost").innerHTML += 
                        "<tr>" +
                            "<td>"+data['owner'][i]['namakost']+"</td>" +
                            "<td>"+data['owner'][i]['alamat']+"</td>" +
                            "<td>"+data['owner'][i]['emailkost']+"</td>" +
                        "</tr>";
                    }
                }
            },
            error : function(data){
                alert('Error mengambil data detail admin, Error: ' + data);
            }
        });
    }

    function konfirmasiHapus(kodeOwner) {
        swal({
            title: "Anda yakin akan menghapus data owner ini?",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Hapus",
            cancelButtonText: "Batal",
            closeOnConfirm: false,
        }, function () {
            var url = '{{ route("super-owner.owner-delete", ":kodeOwner") }}'
            url = url.replace(':kodeOwner', kodeOwner);
            $(location).attr('href', url);
        });
    }

</script>

@endsection

@include('layouts.footer')