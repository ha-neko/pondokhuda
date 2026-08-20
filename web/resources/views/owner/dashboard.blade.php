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
<!-- <div class="row clearfix">
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-pink hover-expand-effect">
            <div class="icon">
                <i class="material-icons">playlist_add_check</i>
            </div>
            <div class="content">
                <div class="text">NEW TASKS</div>
                <div class="number count-to" data-from="0" data-to="125" data-speed="15" data-fresh-interval="20"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-cyan hover-expand-effect">
            <div class="icon">
                <i class="material-icons">help</i>
            </div>
            <div class="content">
                <div class="text">NEW TICKETS</div>
                <div class="number count-to" data-from="0" data-to="257" data-speed="1000" data-fresh-interval="20"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-light-green hover-expand-effect">
            <div class="icon">
                <i class="material-icons">forum</i>
            </div>
            <div class="content">
                <div class="text">NEW COMMENTS</div>
                <div class="number count-to" data-from="0" data-to="243" data-speed="1000" data-fresh-interval="20"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-orange hover-expand-effect">
            <div class="icon">
                <i class="material-icons">person_add</i>
            </div>
            <div class="content">
                <div class="text">NEW VISITORS</div>
                <div class="number count-to" data-from="0" data-to="1225" data-speed="1000" data-fresh-interval="20"></div>
            </div>
        </div>
    </div>
</div> -->
<!-- #END# Widgets -->

<!-- CARD -->
<!-- PENYEWA -->
<div class="row clearfix">
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
        <div class="card">
            <div class="body bg-teal">
                <div class="font-bold m-b--35">INFORMASI KAMAR</div>
                <ul class="dashboard-stat-list">
                    <li>
                        Kamar Terisi
                        <span class="pull-right">{{ $report['report'][0]['infokamar'][0]['terisi'] }}</span>
                    </li>
                    <li>
                        Kamar Kosong
                        <span class="pull-right">{{ $report['report'][0]['infokamar'][2]['kamarkosong'] }}</span>
                    </li>
                    <li>
                        % Kamar Terisi
                        <span class="pull-right">{{ $report['report'][0]['infokamar'][3]['persentasekamarterisi'] }}%</span>
                    </li>
                    <li>
                        % Kamar Kosong
                        <span class="pull-right">{{ $report['report'][0]['infokamar'][4]['persentasekamarkosong'] }}%</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- END OF PENYEWA -->
    <!-- KELUHAN -->
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
        <div class="card">
            <div class="body bg-cyan">
                <div class="m-b--35 font-bold">KELUHAN</div>
                <ul class="dashboard-stat-list">
                    <li>
                        Banyak Keluhan
                        <span class="pull-right">
                            {{ $report['report'][1]['keluhan'][0]['total'] }}
                        </span>
                    </li>
                    <li>
                        % Pelaporan
                        <span class="pull-right">
                            {{ $report['report'][1]['keluhan'][1]['pelaporan'] }}%
                        </span>
                    </li>
                    <li>
                        % Dikerjakan
                        <span class="pull-right">
                            {{ $report['report'][1]['keluhan'][2]['dikerjakan'] }}%
                        </span>
                    </li>
                    <li>
                        % Selesai
                        <span class="pull-right">
                            {{ $report['report'][1]['keluhan'][3]['selesai'] }}%
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- END OF KELUHAN -->
    <!-- SOMETHING -->
    <!-- <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
        <div class="card">
        </div>
    </div> -->
    <!-- END OF SOMETHING -->
</div>
<!-- END OF CARD -->
<!-- INFORMASI SEWA -->
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>
                    Informasi Sewa
                </h2>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table table-hover dashboard-task-infos js-basic-example dataTable">
                        <thead>
                            <tr>
                                <th>No. Kamar</th>
                                <th>Nama Penyewa</th>
                                <th>Status</th>
                                <th>Tgl. Jatuh Tempo</th>
                                <th>Sisa Hari</th>
                                <th>Progres Menuju Bayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($report['informasisewa'] != null)
                                @for($i = 0; $i < count($report['informasisewa']); ++$i)
                                    @if($i == 0 )
                                        @if(count($report['informasisewa']) > 1)
                                        @if($report['informasisewa'][$i]['nokamar'] == $report['informasisewa'][$i + 1]['nokamar'])
                                                <tr>
                                                    <td>{{ $report['informasisewa'][$i]['nokamar'] }}</td>
                                                    <td>
                                                        {{ $report['informasisewa'][$i]['namapenyewa'].', '.$report['informasisewa'][$i+1]['namapenyewa'] }}
                                                    </td>
                                                    @if($report['informasisewa'][$i]['status'] == "Aman")
                                                    <td><span class="label bg-green">{{ $report['informasisewa'][$i]['status'] }}</span></td>
                                                    <td>
                                                        @php
                                                        $date = date_create($report['informasisewa'][$i]['tglnext']);
                                                        echo date_format($date, "d - M - Y");
                                                        @endphp
                                                    </td>
                                                    <td>{{ $report['informasisewa'][$i]['sisahari'] }} Hari</td>
                                                    <td>
                                                        <div class="progress">
                                                            <div class="progress-bar bg-green" role="progressbar" aria-valuenow="{{ $report['informasisewa'][$i]['persentase'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $report['informasisewa'][$i]['persentase'] }}%"></div>
                                                        </div>
                                                    </td>
                                                    @elseif($report['informasisewa'][$i]['status'] == "Warning")
                                                    <td><span class="label bg-red">{{ $report['informasisewa'][$i]['status'] }}</span></td>
                                                    <td>
                                                        @php
                                                        $date = date_create($report['informasisewa'][$i]['tglnext']);
                                                        echo date_format($date, "d - M - Y");
                                                        @endphp
                                                    </td>
                                                    <td>{{ $report['informasisewa'][$i]['sisahari'] }} Hari</td>
                                                    <td>
                                                        <div class="progress">
                                                            <div class="progress-bar bg-red" role="progressbar" aria-valuenow="{{ $report['informasisewa'][$i]['persentase'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $report['informasisewa'][$i]['persentase'] }}%"></div>
                                                        </div>
                                                    </td>
                                                    @elseif($report['informasisewa'][$i]['status'] == "Jatuh Tempo")
                                                    <td><span class="label bg-brown">{{ $report['informasisewa'][$i]['status'] }}</span></td>
                                                    <td>
                                                        @php
                                                        $date = date_create($report['informasisewa'][$i]['tglnext']);
                                                        echo date_format($date, "d - M - Y");
                                                        @endphp
                                                    </td>
                                                    <td>{{ $report['informasisewa'][$i]['sisahari'] }} Hari</td>
                                                    <td>
                                                        <div class="progress">
                                                            <div class="progress-bar bg-brown" role="progressbar" aria-valuenow="{{ $report['informasisewa'][$i]['persentase'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $report['informasisewa'][$i]['persentase'] }}%"></div>
                                                        </div>
                                                    </td>
                                                    @endif
                                                </tr>
                                            @else
                                                <tr>
                                                    <td>{{ $report['informasisewa'][$i]['nokamar'] }}</td>
                                                    <td>
                                                        {{ $report['informasisewa'][$i]['namapenyewa'] }}
                                                    </td>
                                                    @if($report['informasisewa'][$i]['status'] == "Aman")
                                                    <td><span class="label bg-green">{{ $report['informasisewa'][$i]['status'] }}</span></td>
                                                    <td>
                                                        @php
                                                        $date = date_create($report['informasisewa'][$i]['tglnext']);
                                                        echo date_format($date, "d - M - Y");
                                                        @endphp
                                                    </td>
                                                    <td>{{ $report['informasisewa'][$i]['sisahari'] }} Hari</td>
                                                    <td>
                                                        <div class="progress">
                                                            <div class="progress-bar bg-green" role="progressbar" aria-valuenow="{{ $report['informasisewa'][$i]['persentase'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $report['informasisewa'][$i]['persentase'] }}%"></div>
                                                        </div>
                                                    </td>
                                                    @elseif($report['informasisewa'][$i]['status'] == "Warning")
                                                    <td><span class="label bg-red">{{ $report['informasisewa'][$i]['status'] }}</span></td>
                                                    <td>
                                                        @php
                                                        $date = date_create($report['informasisewa'][$i]['tglnext']);
                                                        echo date_format($date, "d - M - Y");
                                                        @endphp
                                                    </td>
                                                    <td>{{ $report['informasisewa'][$i]['sisahari'] }} Hari</td>
                                                    <td>
                                                        <div class="progress">
                                                            <div class="progress-bar bg-red" role="progressbar" aria-valuenow="{{ $report['informasisewa'][$i]['persentase'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $report['informasisewa'][$i]['persentase'] }}%"></div>
                                                        </div>
                                                    </td>
                                                    @elseif($report['informasisewa'][$i]['status'] == "Jatuh Tempo")
                                                    <td><span class="label bg-brown">{{ $report['informasisewa'][$i]['status'] }}</span></td>
                                                    <td>
                                                        @php
                                                        $date = date_create($report['informasisewa'][$i]['tglnext']);
                                                        echo date_format($date, "d - M - Y");
                                                        @endphp
                                                    </td>
                                                    <td>{{ $report['informasisewa'][$i]['sisahari'] }} Hari</td>
                                                    <td>
                                                        <div class="progress">
                                                            <div class="progress-bar bg-brown" role="progressbar" aria-valuenow="{{ $report['informasisewa'][$i]['persentase'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $report['informasisewa'][$i]['persentase'] }}%"></div>
                                                        </div>
                                                    </td>
                                                    @endif
                                                </tr>
                                            @endif
                                        @else
                                            <tr>
                                                <td>{{ $report['informasisewa'][$i]['nokamar'] }}</td>
                                                <td>
                                                    {{ $report['informasisewa'][$i]['namapenyewa'] }}
                                                </td>
                                                @if($report['informasisewa'][$i]['status'] == "Aman")
                                                <td><span class="label bg-green">{{ $report['informasisewa'][$i]['status'] }}</span></td>
                                                <td>
                                                    @php
                                                    $date = date_create($report['informasisewa'][$i]['tglnext']);
                                                    echo date_format($date, "d - M - Y");
                                                    @endphp
                                                </td>
                                                <td>{{ $report['informasisewa'][$i]['sisahari'] }} Hari</td>
                                                <td>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-green" role="progressbar" aria-valuenow="{{ $report['informasisewa'][$i]['persentase'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $report['informasisewa'][$i]['persentase'] }}%"></div>
                                                    </div>
                                                </td>
                                                @elseif($report['informasisewa'][$i]['status'] == "Warning")
                                                <td><span class="label bg-red">{{ $report['informasisewa'][$i]['status'] }}</span></td>
                                                <td>
                                                    @php
                                                    $date = date_create($report['informasisewa'][$i]['tglnext']);
                                                    echo date_format($date, "d - M - Y");
                                                    @endphp
                                                </td>
                                                <td>{{ $report['informasisewa'][$i]['sisahari'] }} Hari</td>
                                                <td>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-red" role="progressbar" aria-valuenow="{{ $report['informasisewa'][$i]['persentase'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $report['informasisewa'][$i]['persentase'] }}%"></div>
                                                    </div>
                                                </td>
                                                @elseif($report['informasisewa'][$i]['status'] == "Jatuh Tempo")
                                                <td><span class="label bg-brown">{{ $report['informasisewa'][$i]['status'] }}</span></td>
                                                <td>
                                                    @php
                                                    $date = date_create($report['informasisewa'][$i]['tglnext']);
                                                    echo date_format($date, "d - M - Y");
                                                    @endphp
                                                </td>
                                                <td>{{ $report['informasisewa'][$i]['sisahari'] }} Hari</td>
                                                <td>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-brown" role="progressbar" aria-valuenow="{{ $report['informasisewa'][$i]['persentase'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $report['informasisewa'][$i]['persentase'] }}%"></div>
                                                    </div>
                                                </td>
                                                @endif
                                            </tr>
                                        @endif
                                    @else
                                        @if($i == count($report['informasisewa']) - 1)
                                            @if($report['informasisewa'][$i]['nokamar'] ==
                                            $report['informasisewa'][$i - 1]['nokamar'])
                                            @else
                                                <tr>
                                                    <td>{{ $report['informasisewa'][$i]['nokamar'] }}</td>
                                                    <td>
                                                        {{ $report['informasisewa'][$i]['namapenyewa'] }}
                                                    </td>
                                                    @if($report['informasisewa'][$i]['status'] == "Aman")
                                                    <td><span class="label bg-green">{{ $report['informasisewa'][$i]['status'] }}</span></td>
                                                    <td>
                                                        @php
                                                        $date = date_create($report['informasisewa'][$i]['tglnext']);
                                                        echo date_format($date, "d - M - Y");
                                                        @endphp
                                                    </td>
                                                    <td>{{ $report['informasisewa'][$i]['sisahari'] }} Hari</td>
                                                    <td>
                                                        <div class="progress">
                                                            <div class="progress-bar bg-green" role="progressbar" aria-valuenow="{{ $report['informasisewa'][$i]['persentase'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $report['informasisewa'][$i]['persentase'] }}%"></div>
                                                        </div>
                                                    </td>
                                                    @elseif($report['informasisewa'][$i]['status'] == "Warning")
                                                    <td><span class="label bg-red">{{ $report['informasisewa'][$i]['status'] }}</span></td>
                                                    <td>
                                                        @php
                                                        $date = date_create($report['informasisewa'][$i]['tglnext']);
                                                        echo date_format($date, "d - M - Y");
                                                        @endphp
                                                    </td>
                                                    <td>{{ $report['informasisewa'][$i]['sisahari'] }} Hari</td>
                                                    <td>
                                                        <div class="progress">
                                                            <div class="progress-bar bg-red" role="progressbar" aria-valuenow="{{ $report['informasisewa'][$i]['persentase'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $report['informasisewa'][$i]['persentase'] }}%"></div>
                                                        </div>
                                                    </td>
                                                    @elseif($report['informasisewa'][$i]['status'] == "Jatuh Tempo")
                                                    <td><span class="label bg-brown">{{ $report['informasisewa'][$i]['status'] }}</span></td>
                                                    <td>
                                                        @php
                                                        $date = date_create($report['informasisewa'][$i]['tglnext']);
                                                        echo date_format($date, "d - M - Y");
                                                        @endphp
                                                    </td>
                                                    <td>{{ $report['informasisewa'][$i]['sisahari'] }} Hari</td>
                                                    <td>
                                                        <div class="progress">
                                                            <div class="progress-bar bg-brown" role="progressbar" aria-valuenow="{{ $report['informasisewa'][$i]['persentase'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $report['informasisewa'][$i]['persentase'] }}%"></div>
                                                        </div>
                                                    </td>
                                                    @endif
                                                </tr>
                                            @endif
                                        @else
                                            @if($report['informasisewa'][$i]['nokamar'] == $report['informasisewa'][$i + 1]['nokamar'])
                                                <tr>
                                                    <td>{{ $report['informasisewa'][$i]['nokamar'] }}</td>
                                                    <td>
                                                        {{ $report['informasisewa'][$i]['namapenyewa'].', '.$report['informasisewa'][$i+1]['namapenyewa'] }}
                                                    </td>
                                                    @if($report['informasisewa'][$i]['status'] == "Aman")
                                                    <td><span class="label bg-green">{{ $report['informasisewa'][$i]['status'] }}</span></td>
                                                    <td>
                                                        @php
                                                        $date = date_create($report['informasisewa'][$i]['tglnext']);
                                                        echo date_format($date, "d - M - Y");
                                                        @endphp
                                                    </td>
                                                    <td>{{ $report['informasisewa'][$i]['sisahari'] }} Hari</td>
                                                    <td>
                                                        <div class="progress">
                                                            <div class="progress-bar bg-green" role="progressbar" aria-valuenow="{{ $report['informasisewa'][$i]['persentase'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $report['informasisewa'][$i]['persentase'] }}%"></div>
                                                        </div>
                                                    </td>
                                                    @elseif($report['informasisewa'][$i]['status'] == "Warning")
                                                    <td><span class="label bg-red">{{ $report['informasisewa'][$i]['status'] }}</span></td>
                                                    <td>
                                                        @php
                                                        $date = date_create($report['informasisewa'][$i]['tglnext']);
                                                        echo date_format($date, "d - M - Y");
                                                        @endphp
                                                    </td>
                                                    <td>{{ $report['informasisewa'][$i]['sisahari'] }} Hari</td>
                                                    <td>
                                                        <div class="progress">
                                                            <div class="progress-bar bg-red" role="progressbar" aria-valuenow="{{ $report['informasisewa'][$i]['persentase'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $report['informasisewa'][$i]['persentase'] }}%"></div>
                                                        </div>
                                                    </td>
                                                    @elseif($report['informasisewa'][$i]['status'] == "Jatuh Tempo")
                                                    <td><span class="label bg-brown">{{ $report['informasisewa'][$i]['status'] }}</span></td>
                                                    <td>
                                                        @php
                                                        $date = date_create($report['informasisewa'][$i]['tglnext']);
                                                        echo date_format($date, "d - M - Y");
                                                        @endphp
                                                    </td>
                                                    <td>{{ $report['informasisewa'][$i]['sisahari'] }} Hari</td>
                                                    <td>
                                                        <div class="progress">
                                                            <div class="progress-bar bg-brown" role="progressbar" aria-valuenow="{{ $report['informasisewa'][$i]['persentase'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $report['informasisewa'][$i]['persentase'] }}%"></div>
                                                        </div>
                                                    </td>
                                                    @endif
                                                </tr>
                                            @elseif($report['informasisewa'][$i]['nokamar'] ==
                                            $report['informasisewa'][$i - 1]['nokamar'])
                                            @else
                                                <tr>
                                                    <td>{{ $report['informasisewa'][$i]['nokamar'] }}</td>
                                                    <td>
                                                        {{ $report['informasisewa'][$i]['namapenyewa'] }}
                                                    </td>
                                                    @if($report['informasisewa'][$i]['status'] == "Aman")
                                                    <td><span class="label bg-green">{{ $report['informasisewa'][$i]['status'] }}</span></td>
                                                    <td>
                                                        @php
                                                        $date = date_create($report['informasisewa'][$i]['tglnext']);
                                                        echo date_format($date, "d - M - Y");
                                                        @endphp
                                                    </td>
                                                    <td>{{ $report['informasisewa'][$i]['sisahari'] }} Hari</td>
                                                    <td>
                                                        <div class="progress">
                                                            <div class="progress-bar bg-green" role="progressbar" aria-valuenow="{{ $report['informasisewa'][$i]['persentase'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $report['informasisewa'][$i]['persentase'] }}%"></div>
                                                        </div>
                                                    </td>
                                                    @elseif($report['informasisewa'][$i]['status'] == "Warning")
                                                    <td><span class="label bg-red">{{ $report['informasisewa'][$i]['status'] }}</span></td>
                                                    <td>
                                                        @php
                                                        $date = date_create($report['informasisewa'][$i]['tglnext']);
                                                        echo date_format($date, "d - M - Y");
                                                        @endphp
                                                    </td>
                                                    <td>{{ $report['informasisewa'][$i]['sisahari'] }} Hari</td>
                                                    <td>
                                                        <div class="progress">
                                                            <div class="progress-bar bg-red" role="progressbar" aria-valuenow="{{ $report['informasisewa'][$i]['persentase'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $report['informasisewa'][$i]['persentase'] }}%"></div>
                                                        </div>
                                                    </td>
                                                    @elseif($report['informasisewa'][$i]['status'] == "Jatuh Tempo")
                                                    <td><span class="label bg-brown">{{ $report['informasisewa'][$i]['status'] }}</span></td>
                                                    <td>
                                                        @php
                                                        $date = date_create($report['informasisewa'][$i]['tglnext']);
                                                        echo date_format($date, "d - M - Y");
                                                        @endphp
                                                    </td>
                                                    <td>{{ $report['informasisewa'][$i]['sisahari'] }} Hari</td>
                                                    <td>
                                                        <div class="progress">
                                                            <div class="progress-bar bg-brown" role="progressbar" aria-valuenow="{{ $report['informasisewa'][$i]['persentase'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $report['informasisewa'][$i]['persentase'] }}%"></div>
                                                        </div>
                                                    </td>
                                                    @endif
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
<!-- END OF INFORMASI SEWA -->

@endsection

@section('js-content')

<script type="text/javascript">

	$(document).ready(function () {
	    $('.js-basic-example').DataTable({
            columnDefs: [
                { type: 'natural', targets: 0 }
            ],
            responsive: true
        });
	});
    
</script>

@endsection

@include('layouts.footer')