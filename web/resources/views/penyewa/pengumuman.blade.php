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

<!-- Widgets -->
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
        	@if ($pengumuman['pengumuman'] == null)
        	<div class="header"><b>Pengumuman tidak ada</b></div>
        	@else
        	@foreach($pengumuman['pengumuman'] as $u)
            <div class="header" style="word-break: break-all;">
                <b>{{ $u['judul'] }}</b>
            </div>
            <div class="body" style="border-bottom: 1px solid #48f23c; word-break: break-all;">
            	<p align="justify" style="word-break: break-all;">
            	{{ $u['berita'] }}
            	</p>
            	<p align="right" style="opacity: 0.8; font-size: 13px; word-break: break-all;">
            		Published : {{ $u['tglpublish'] }}<br>
            		Update : {{ $u['lastupdate'] }}
            	</p>
            </div>
            @endforeach
            @endif
        </div>
    </div>
</div>
<!-- #END# Widgets -->

@extends('penyewa.whatsapp-admin')

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