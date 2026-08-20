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
<div class="ph-overview-grid">
    <div class="ph-overview-tile">
        <i class="material-icons">person</i>
        <strong>{{ $report['report'][0]['jumlah_owner'] }}</strong>
        <span>Pemilik terdaftar</span>
    </div>
    <div class="ph-overview-tile">
        <i class="material-icons">location_city</i>
        <strong>{{ $report['report'][0]['jumlah_kost'] }}</strong>
        <span>Kost dikelola</span>
    </div>
    <div class="ph-overview-tile">
        <i class="material-icons">hotel</i>
        <strong>{{ $report['report'][0]['jumlah_kamar'] }}</strong>
        <span>Total kamar</span>
    </div>
    <div class="ph-overview-tile">
        <i class="material-icons">group</i>
        <strong>{{ $report['report'][0]['jumlah_penyewa'] }}</strong>
        <span>Penyewa aktif</span>
    </div>
</div>

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
