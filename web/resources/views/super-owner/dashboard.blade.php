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
<!-- CARD -->
<!-- OWNER, KOST, KAMAR, PENYEWA -->
<div class="row clearfix">
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
        <div class="card">
            <div class="body bg-teal">
                <div class="font-bold m-b--35">INFORMASI KOST</div>
                <ul class="dashboard-stat-list">
                    <li>
                        Jumlah Owner Terdaftar
                        <span class="pull-right">{{ $report['report'][0]['jumlah_owner'] }}</span>
                    </li>
                    <li>
                        Jumlah Kost Terdaftar
                        <span class="pull-right">{{ $report['report'][0]['jumlah_kost'] }}</span>
                    </li>
                    <li>
                        Jumlah Kamar Terdaftar
                        <span class="pull-right">{{ $report['report'][0]['jumlah_kamar'] }}</span>
                    </li>
                    <li>
                        Jumlah Penyewa Aktif Terdaftar
                        <span class="pull-right">{{ $report['report'][0]['jumlah_penyewa'] }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- END OF OWNER, KOST, KAMAR, PENYEWA -->
    <!-- SOMETHING 1 -->
    <!-- <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
        <div class="card">
        </div>
    </div> -->
    <!-- SOMETHING 1 -->
    <!-- SOMETHING 2 -->
    <!-- <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
        <div class="card">
        </div>
    </div> -->
    <!-- END OF SOMETHING 2 -->
</div>
<!-- END OF CARD -->

<!-- <div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>
                    This is Menu Action
                </h2>
                <ul class="header-dropdown m-r--5">
                    <li class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons">more_vert</i>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <li><a href="javascript:void(0);">Action</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="body">
                
            </div>
        </div>
    </div>
</div> -->

@endsection

@section('js-content')

<script type="text/javascript">

	$(document).ready(function () {
	    //Widgets count
	    $('.count-to').countTo();
	});

</script>

@endsection

@include('layouts.footer')