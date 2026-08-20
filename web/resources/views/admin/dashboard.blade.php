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
        <div class="ph-dashboard-panel ph-dashboard-panel--violet">
            <div class="ph-dashboard-panel__head">
                <span class="ph-dashboard-panel__eyebrow">Tren pembayaran</span>
                <span class="ph-dashboard-panel__icon"><i class="material-icons">monitoring</i></span>
            </div>
            <div class="sparkline" data-type="line" data-spot-Radius="3" data-highlight-Spot-Color="#fff" data-highlight-Line-Color="#fff"
                 data-min-Spot-Color="#fff" data-max-Spot-Color="#fff" data-spot-Color="#fff"
                 data-offset="90" data-width="100%" data-height="108px" data-line-Width="2" data-line-Color="rgba(255,255,255,0.86)"
                 data-fill-Color="rgba(255,255,255,0.08)">
                @if($report['pendapatan'] != null)
                    @for ($i=0; $i < count($report['pendapatan']); $i++)
                        @php
                            $date1 = strtotime($report['pendapatan'][$i]['tanggal']);
                            $date2 = strtotime(date("Y-m-d"));
                            $datediff = $date2 - $date1;
                            $th = round($datediff / (60 * 60 * 24));
                        @endphp
                        @if((int)$th < 301)
                            @if($i+1 != count($report['pendapatan']))
                                @if($report['pendapatan'][$i]['tanggal'] != $report['pendapatan'][$i + 1]['tanggal'])
                                    {{ $report['pendapatan'][$i]['jumlah'] }},
                                @endif
                            @else
                                {{ $report['pendapatan'][$i]['jumlah'] }}
                            @endif
                        @endif
                    @endfor
                @else
                    0
                @endif
            </div>
            <div class="ph-dashboard-panel__label">10 bulan terakhir</div>
        </div>
    </div>
</div>
<div class="row clearfix">
    <!-- Task Info -->
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="card ph-table-card">
            <div class="header">
                <h2>Informasi Penyewa <small>Status jatuh tempo dan progres pembayaran penghuni</small></h2>
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
    <!-- #END# Task Info -->
</div>
<!-- #END# Widgets -->

@endsection
@section('js-content')

<script type="text/javascript">
    function penyewa() {
        // Check kamar terisi bilangan bulat
        if (document.getElementById("ktbc").checked == true) {
            document.getElementById("kamarterisibulat").style.display = "";
        }
        else {
            document.getElementById("kamarterisibulat").style.display = "none";
        }

        // Check kamar kosong bilangan bulat
        if (document.getElementById("kkbc").checked == true) {
            document.getElementById("kamarkosongbulat").style.display = "";
        }
        else {
            document.getElementById("kamarkosongbulat").style.display = "none";
        }

        // Check kamar terisi persentase
        if (document.getElementById("pkt").checked == true) {
            document.getElementById("persentasekamarterisi").style.display = "";
        }
        else {
            document.getElementById("persentasekamarterisi").style.display = "none";
        }

        // Check kamar kosong persentase
        if (document.getElementById("pkk").checked == true) {
            document.getElementById("persentasekamarkosong").style.display = "";
        }
        else {
            document.getElementById("persentasekamarkosong").style.display = "none";
        }
    }
    function keluhan() {
        if (document.getElementById("bk").checked == true) {
            document.getElementById("banyakkeluhan").style.display = "";
        }
        else {
            document.getElementById("banyakkeluhan").style.display = "none";
        }
        if (document.getElementById("pp").checked == true) {
            document.getElementById("keluhanpelaporan").style.display = "";
        }
        else {
            document.getElementById("keluhanpelaporan").style.display = "none";
        }
        if (document.getElementById("pd").checked == true) {
            document.getElementById("keluhandikerjakan").style.display = "";
        }
        else {
            document.getElementById("keluhandikerjakan").style.display = "none";
        }
        if (document.getElementById("ps").checked == true) {
            document.getElementById("keluhanselesai").style.display = "";
        }
        else {
            document.getElementById("keluhanselesai").style.display = "none";
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
	$(document).ready(function () {
	    //Widgets count
	    $('.count-to').countTo();
	});

</script>
<!-- ChartJs -->
<script src="{{{ URL::asset('adminbsb/plugins/chartjs/Chart.bundle.js') }}}"></script>

<!-- Sparkline Chart Plugin Js -->
<script src="{{{ URL::asset('adminbsb/plugins/jquery-sparkline/jquery.sparkline.js') }}}"></script>
<script src="{{{ URL::asset('adminbsb/js/pages/charts/sparkline.js') }}}"></script>

@endsection

@include('layouts.footer')
