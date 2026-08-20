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

<!-- Basic Examples -->
<div class="row clearfix">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="card">
			<div class="header">
                <h2>
                    Tabel Frekuensi Penyewa Masuk - Keluar
                </h2>
            </div>
            <div class="body">
                <div class="table-responsive">
                	<table id="dataTable" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Periode Bulan</th>
                                <th>Penyewa Masuk</th>
                                <th>Penyewa Keluar</th>
                                <th>Frekuensi Kumulatif</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reportPenyewas['report'][2]['datapenyewa'][0]['penyewamasuk'] as $masuk)
                            <tr>
                                <th>{{$masuk['bulan']}}</th>
                                <th>{{$masuk['jumlah']}}</th>
                                <th></th>
                                <th></th>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>
                    Grafik Frekuensi Penyewa Masuk - Keluar
                </h2>
            </div>
            <div class="body">
                <div id="bar_chart" class="justify-content-middle" width="100%"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js-content')

<script type="text/javascript">

    $(document).ready(function () {
        Morris.Bar({
            element: 'bar_chart',
            data: {!! json_encode($reportPenyewas['report'][2]['datapenyewa'][0]['penyewamasuk']) !!},
            xkey: 'bulan',
            ykeys: ['jumlah'],
            labels: ['Jumlah'],
            barColors: ['rgb(0, 188, 212)'],
        });
    });

</script>

@endsection

@include('layouts.footer')