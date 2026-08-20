@include('layouts.header')
@extends('layouts.sidebar')
@extends('layouts.menubar')
@section('content')

<div class="block-header">
	<h2>{{ $data['pageTitle'] }}</h2>
</div>

@if(Session::has('message'))
<p class="alert {{ Session::get('alert-class') }} alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ Session::get('message') }}
</p>
@endif

<!-- LIST ADMIN -->
<div class="row clearfix">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="card">
			<div class="header">
                <h2>
                    List Data Admin
                </h2>
                <ul class="header-dropdown m-r--5">
                    <li class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons">more_vert</i>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <li><a href="{{route('owner.admin-add')}}">Tambah Admin</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="body">
                <div class="table-responsive">
                	<table id="dataTable" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Kode Admin</th>
                                <th>Nama Admin</th>
                                <th>Nomor Pin</th>
                                <th>Nomor Telepon</th>
                                <th>Kost Yang Dikelola</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $kost = "";
                            $index = 0;
                            @endphp
                            @if($admins['admin'] != null)
                                @for($i = 0; $i < count($admins['admin']); ++$i)
                                    @if($i == count($admins['admin']) - 1)
                                        @if(count($admins['admin']) == 1)
                                        <tr>
                                            <td>{{ $admins['admin'][$i]['kode'] }}</td>
                                            <td>{{ $admins['admin'][$i]['nama'] }}</td>
                                            <td>{{ $admins['admin'][$i]['pin'] }}</td>
                                            <td>{{ $admins['admin'][$i]['nomortelepon'] }}</td>
                                            <td>{{ $admins['admin'][$i]['namakost'] }}</td>
                                            <td><a class="btn btn-primary" onclick="getDetailAdmin('{{ $admins["admin"][$i]["kode"] }}');" href="javascript:void(0);" data-toggle="modal" data-target="#detailAdmin"> Detail </a>
                                                <a class="btn btn-warning" href="{{ route('owner.admin-edit', $admins["admin"][$i]["kode"]) }}"> Ubah </a></td>
                                        </tr>
                                        @else
                                            @if($admins['admin'][$i]['kode'] == $admins['admin'][$i - 1]['kode'])
                                                @php

                                                if($index == 0)
                                                    $kost = $kost.$admins['admin'][$i]['namakost'];
                                                else
                                                    $kost = $kost.", ".$admins['admin'][$i]['namakost'];

                                                $index++;

                                                @endphp
                                                <tr>
                                                    <td>{{ $admins['admin'][$i]['kode'] }}</td>
                                                    <td>{{ $admins['admin'][$i]['nama'] }}</td>
                                                    <td>{{ $admins['admin'][$i]['pin'] }}</td>
                                                    <td>{{ $admins['admin'][$i]['nomortelepon'] }}</td>
                                                    <td>{{ $kost }}</td>
                                                    <td><a class="btn btn-primary" onclick="getDetailAdmin('{{ $admins["admin"][$i]["kode"] }}');" href="javascript:void(0);" data-toggle="modal" data-target="#detailAdmin"> Detail </a>
                                                        <a class="btn btn-warning" href="{{ route('owner.admin-edit', $admins["admin"][$i]["kode"]) }}"> Ubah </a></td>
                                                </tr>
                                                @php $kost = ""; $index = 0; @endphp
                                            @else
                                            <tr>
                                                <td>{{ $admins['admin'][$i]['kode'] }}</td>
                                                <td>{{ $admins['admin'][$i]['nama'] }}</td>
                                                <td>{{ $admins['admin'][$i]['pin'] }}</td>
                                                <td>{{ $admins['admin'][$i]['nomortelepon'] }}</td>
                                                <td>{{ $admins['admin'][$i]['namakost'] }}</td>
                                                <td><a class="btn btn-primary" onclick="getDetailAdmin('{{ $admins["admin"][$i]["kode"] }}');" href="javascript:void(0);" data-toggle="modal" data-target="#detailAdmin"> Detail </a>
                                                    <a class="btn btn-warning" href="{{ route('owner.admin-edit', $admins["admin"][$i]["kode"]) }}"> Ubah </a></td>
                                            </tr>
                                            @endif
                                        @endif
                                    @else
                                        @if($admins['admin'][$i]['kode'] == $admins['admin'][$i + 1]['kode'])
                                            @php

                                            if($index == 0)
                                                $kost = $kost.$admins['admin'][$i]['namakost'];
                                            else
                                                $kost = $kost.", ".$admins['admin'][$i]['namakost'];

                                            $index++;

                                            @endphp
                                        @else
                                            @if($i > 0)
                                                @if($admins['admin'][$i]['kode'] == $admins['admin'][$i - 1]['kode'])
                                                    @php

                                                    if($index == 0)
                                                        $kost = $kost.$admins['admin'][$i]['namakost'];
                                                    else
                                                        $kost = $kost.", ".$admins['admin'][$i]['namakost'];

                                                    $index++;

                                                    @endphp
                                                    <tr>
                                                        <td>{{ $admins['admin'][$i]['kode'] }}</td>
                                                        <td>{{ $admins['admin'][$i]['nama'] }}</td>
                                                        <td>{{ $admins['admin'][$i]['pin'] }}</td>
                                                        <td>{{ $admins['admin'][$i]['nomortelepon'] }}</td>
                                                        <td>{{ $kost }}</td>
                                                        <td><a class="btn btn-primary" onclick="getDetailAdmin('{{ $admins["admin"][$i]["kode"] }}');" href="javascript:void(0);" data-toggle="modal" data-target="#detailAdmin"> Detail </a>
                                                            <a class="btn btn-warning" href="{{ route('owner.admin-edit', $admins["admin"][$i]["kode"]) }}"> Ubah </a></td>
                                                    </tr>
                                                    @php $kost = ""; $index = 0; @endphp
                                                @else
                                                <tr>
                                                    <td>{{ $admins['admin'][$i]['kode'] }}</td>
                                                    <td>{{ $admins['admin'][$i]['nama'] }}</td>
                                                    <td>{{ $admins['admin'][$i]['pin'] }}</td>
                                                    <td>{{ $admins['admin'][$i]['nomortelepon'] }}</td>
                                                    <td>{{ $admins['admin'][$i]['namakost'] }}</td>
                                                    <td><a class="btn btn-primary" onclick="getDetailAdmin('{{ $admins["admin"][$i]["kode"] }}');" href="javascript:void(0);" data-toggle="modal" data-target="#detailAdmin"> Detail </a>
                                                        <a class="btn btn-warning" href="{{ route('owner.admin-edit', $admins["admin"][$i]["kode"]) }}"> Ubah </a></td>
                                                </tr>
                                                @endif
                                            @else
                                            <tr>
                                                <td>{{ $admins['admin'][$i]['kode'] }}</td>
                                                <td>{{ $admins['admin'][$i]['nama'] }}</td>
                                                <td>{{ $admins['admin'][$i]['pin'] }}</td>
                                                <td>{{ $admins['admin'][$i]['nomortelepon'] }}</td>
                                                <td>{{ $admins['admin'][$i]['namakost'] }}</td>
                                                <td><a class="btn btn-primary" onclick="getDetailAdmin('{{ $admins["admin"][$i]["kode"] }}');" href="javascript:void(0);" data-toggle="modal" data-target="#detailAdmin"> Detail </a>
                                                    <a class="btn btn-warning" href="{{ route('owner.admin-edit', $admins["admin"][$i]["kode"]) }}"> Ubah </a></td>
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
</div>
<!-- END OF LIST ADMIN -->

<!-- MODAL DETAIL -->
<div class="modal fade" id="detailAdmin" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-col-{{Auth::user()->ownerkost[$indexKost]['kost']['name_color']}}">
            <div class="modal-header">
            </div>
            <div class="modal-body">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header">
                                <h2>DETAIL DATA ADMIN</h2>
                            </div>
                            <div class="body">
                                <center>
                                    <div class="form-group form-float">
                                        <div id="img-foto-detail" style="background-image: url(''); width: 150px; height: 150px; background-size: cover; background-repeat: no-repeat; background-position: 50% 50%; border-radius: 100px; border: 5px solid #d4d4d6;"></div>
                                        
                                    </div>
                                </center>
                                <div class="form-group form-float">
                                    <label class="form-label">Kode Admin</label>
                                    <div class="form-line">
                                        <input type="text" class="form-control" id="kode-detail" name="kode-detail" readonly="true">
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <label class="form-label">Nama Admin</label>
                                    <div class="form-line">
                                        <input type="text" class="form-control" id="nama-detail" name="nama-detail" readonly="true">
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <label class="form-label">Pin Admin</label>
                                    <div class="form-line">
                                        <input type="number" class="form-control" id="pin-detail" name="pin-detail" readonly="true">
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <label for="kost" id="labelkost">Kost Yang Dikelola</label>
                                    <div class="demo-checkbox" id="checkkost">
                                        @php
                                        $index=0;
                                        @endphp
                                        @if(count(Auth::user()->ownerkost) != 0)
                                            @foreach(Auth::user()->ownerkost as $key => $kost)
                                                <input type="checkbox" name="kost-detail[]" id="{{$kost['kost']['kode_kost']}}" 
                                                value="{{$kost['kost']['kode_kost']}}" disabled="true" />
                                                <label for="{{$kost['kost']['kode_kost']}}">{{$kost['kost']['nama_kost']}}</label>
                                                <br>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
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

    $(function () {
        $('#dataTable').DataTable({
            responsive: true
        });
    });

    function getDetailAdmin(kode) {
        $.ajax({
            type : "post",
            url  : "{{ route('owner.admin-detail') }}",
            data : { _token: "{{csrf_token()}}", kode : kode },
            dataType : "json",
            success :function(data){
                document.getElementById("img-foto-detail").style.backgroundImage = "url('" + data['admin'][0]['urlfoto'] + "')";
                document.getElementById("kode-detail").value = data['admin'][0]['kode'];
                document.getElementById("nama-detail").value = data['admin'][0]['nama'];
                document.getElementById("pin-detail").value = data['admin'][0]['pin'];

                for(i = 0; i < data['admin'].length; i++)
                {
                    $("input[type=checkbox][value!='"+data['admin'][i]['kodekost']+"']").prop('checked',false);
                }
                
                for(i = 0; i < data['admin'].length; i++)
                {
                    $("input[type=checkbox][value='"+data['admin'][i]['kodekost']+"']").prop('checked',true);
                }
            },
            error : function(data){
                alert('Error mengambil data detail admin, Error: ' + data);
            }
        });
    }

</script>

@endsection

@include('layouts.footer')