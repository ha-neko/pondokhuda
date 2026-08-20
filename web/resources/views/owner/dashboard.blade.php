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

<div class="row clearfix ph-dashboard-cards">
    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
        <div class="ph-dashboard-panel ph-dashboard-panel--brand">
            <div class="ph-dashboard-panel__head">
                <span class="ph-dashboard-panel__eyebrow">Okupansi kamar</span>
                <span class="ph-dashboard-panel__icon"><i class="material-icons">hotel</i></span>
            </div>
            <div class="ph-dashboard-panel__value">{{ $report['report'][0]['infokamar'][3]['persentasekamarterisi'] }}%</div>
            <div class="ph-dashboard-panel__label">Kamar sedang terisi</div>
            <div class="ph-dashboard-panel__footer">
                <div><small>Terisi</small><strong>{{ $report['report'][0]['infokamar'][0]['terisi'] }} kamar</strong></div>
                <div><small>Tersedia</small><strong>{{ $report['report'][0]['infokamar'][2]['kamarkosong'] }} kamar</strong></div>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
        <div class="ph-dashboard-panel ph-dashboard-panel--cyan">
            <div class="ph-dashboard-panel__head">
                <span class="ph-dashboard-panel__eyebrow">Keluhan penghuni</span>
                <span class="ph-dashboard-panel__icon"><i class="material-icons">forum</i></span>
            </div>
            <div class="ph-dashboard-panel__value">{{ $report['report'][1]['keluhan'][0]['total'] }}</div>
            <div class="ph-dashboard-panel__label">Total keluhan tercatat</div>
            <div class="ph-dashboard-panel__footer">
                <div><small>Dikerjakan</small><strong>{{ $report['report'][1]['keluhan'][2]['dikerjakan'] }}%</strong></div>
                <div><small>Selesai</small><strong>{{ $report['report'][1]['keluhan'][3]['selesai'] }}%</strong></div>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
        <div class="ph-dashboard-panel ph-dashboard-panel--plain">
            <div class="ph-dashboard-panel__head">
                <span class="ph-dashboard-panel__eyebrow">Aksi cepat</span>
                <span class="ph-dashboard-panel__icon"><i class="material-icons">bolt</i></span>
            </div>
            <div class="ph-quick-actions">
                <a href="{{ route('owner.pembayaran') }}"><i class="material-icons">payments</i> Catat pembayaran <i class="material-icons arrow">chevron_right</i></a>
                <a href="{{ route('owner.keluhan') }}"><i class="material-icons">chat</i> Tinjau keluhan <i class="material-icons arrow">chevron_right</i></a>
                <a href="{{ route('owner.pengumuman') }}"><i class="material-icons">campaign</i> Kelola pengumuman <i class="material-icons arrow">chevron_right</i></a>
            </div>
        </div>
    </div>
</div>
<!-- INFORMASI SEWA -->
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card ph-table-card">
            <div class="header">
                <h2>
                    Informasi Sewa
                    <small>Status jatuh tempo dan progres pembayaran penghuni</small>
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
