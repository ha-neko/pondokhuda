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
                    List Data Kost
                </h2>
                <ul class="header-dropdown m-r--5">
                    <li class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons">more_vert</i>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <li><a href="{{route('owner.kost-add')}}">Tambah Kost</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="body">
                <div class="table-responsive">
                	<table id="dataTable" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Nama Kost</th>
                                <th>Alamat</th>
                                <th>Email Kost</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(Auth::user()->ownerkost as $key => $kost)
                            <tr>
                                <td>{{$kost['kost']['nama_kost']}}</td>
                                <td>{{$kost['kost']['alamat']}}</td>
                                <td>{{$kost['kost']['emailkost']}}</td>
                                <td>
                                    <a href="javascript:void(0);" onclick="getDetailKost({{$key}});" data-toggle="modal" data-target="#detailKost">
                                        <button class="btn btn-primary">Detail</button>
                                    </a>
                                    <a href="{{ route('owner.kost-edit', $kost['kost']['kode_kost']) }}">
                                        <button class="btn btn-warning">Ubah</button>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END OF LIST ADMIN -->

<!-- MODAL DETAIL -->
<div class="modal fade" id="detailKost" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-col-{{Auth::user()->ownerkost[$indexKost]['kost']['name_color']}}">
            <div class="modal-header">
            </div>
            <div class="modal-body">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header">
                                <h2>DETAIL DATA KOST</h2>
                            </div>
                            <div class="body">
                                <center>
                                    <div class="form-group form-float">
                                        <div id="imglogo" style="background-image: url('https://pondok-huda.com/Assets/images/logo/default-logo-black.png'); width: 150px; height: 150px; background-size: cover; background-repeat: no-repeat; background-position: 50% 50%; border-radius: 100px; border: 5px solid #d4d4d6;"></div>
                                    </div>
                                </center>
                                <div class="form-group form-float">
                                    <label class="form-label">Nama Kost</label>
                                    <div class="form-line">
                                        <input type="text" class="form-control" id="namakost" name="namakost" readonly="true">
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <label class="form-label">Alamat Kost</label>
                                    <div class="form-line">
                                        <input type="text" class="form-control" id="alamatkost" name="alamatkost" readonly="true">
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <label class="form-label">Email Kost</label>
                                    <div class="form-line">
                                        <input type="email" class="form-control" id="emailkost" name="emailkost" readonly="true">
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <label class="form-label">Warna Utama</label>
                                    <div class="form-group">
                                        <span id="namecolor" class="">&nbsp;&nbsp;&nbsp;</span>
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
    </section>
    </div>
</div>
<!-- #END# MODAL DETAIL -->

@endsection

@section('js-content')

<script type="text/javascript">

    function getDetailKost(kode)
    {
        $.ajax({
            type : "post",
            url  : "{{ route('owner.kost-detail') }}",
            data : { _token: "{{csrf_token()}}", kode : kode },
            dataType : "json",
            success :function(data){
                document.getElementById("imglogo").style.backgroundImage = "url('https://pondok-huda.com/Assets/images/logo/default-logo-black.png')";

                if (data['kost'][0]['logo'] != "")
                {
                    document.getElementById("imglogo").style.backgroundImage = "url('"+data['kost'][0]['logo']+"')";
                }

                document.getElementById("namakost").value = data['kost'][0]['namakost'];

                document.getElementById("alamatkost").value = data['kost'][0]['alamatkost'];;

                document.getElementById("emailkost").value = data['kost'][0]['emailkost'];

                document.getElementById("namecolor").className = "badge bg-" + data['kost'][0]['namecolor'];

            },
            error : function(data){
                alert('Error mengambil data detail kost, Error: ' + JSON.stringify(data));
            }
        });
    }

    $(function () {
        $('#dataTable').DataTable({
            responsive: true
        });
    });

</script>

@endsection

@include('layouts.footer')