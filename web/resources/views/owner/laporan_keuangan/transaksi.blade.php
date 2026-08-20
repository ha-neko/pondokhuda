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
                    Daftar Transaksi Kost
                </h2>
                <ul class="header-dropdown m-r--5">
                    <li class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons">more_vert</i>
                        </a>
                        <ul class="dropdown-menu pull-right" data-toggle="modal">
                            <li><a href="javascript:void(0);" onclick="printData();">Print Data</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group form-float">
                            <label for="bulan">Pilih Bulan</label>
                            <div class="form-line">
                                <select class="form-control show-tick" name="bulan" id="bulan">
                                    <option value="">-- Pilih Bulan --</option>
                                    @foreach($data['bulans'] as $key=> $bln)
                                    @if(($key + 1) == date('m'))
                                    <option value="{{ $key + 1 }}" selected="selected">{{ $bln }}</option>
                                    @else
                                    <option value="{{ $key + 1 }}">{{ $bln }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group form-float">
                            <label for="tahun">Pilih Tahun</label>
                            <div class="form-line">
                                <select class="form-control show-tick" name="tahun" id="tahun">
                                    <option value="">-- Pilih Tahun --</option>
                                    @foreach($data['tahuns'] as $key=> $thn)
                                    @if($thn == date('Y'))
                                    <option value="{{ $thn }}" selected="selected">{{ $thn }}</option>
                                    @else
                                    <option value="{{ $thn }}">{{ $thn }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="print">
                    <div class="table-responsive">
                        <center>
                            <h5 id="titlePendapatan"> Transaksi Pendapatan Kost Bulan </h5>
                            <table class="table table-bordered table-striped table-hover" border="1" cellspacing="0" cellpadding="10">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Keterangan</th>
                                        <th>Metode</th>
                                        <th>Total Bayar</th>
                                    </tr>
                                </thead>
                                <tbody id="transaksiPendapatan">
                                    @php
                                        $i = 1; $jumlahPendapatan = 0;
                                    @endphp
                                    @if($transaksis[0]['pendapatan'] != null)
                                    @php
                                        asort($transaksis[0]['pendapatan']);
                                    @endphp
                                    @foreach($transaksis[0]['pendapatan'] as $pendapatan)
                                    <tr>
                                        <td> {{ $i }} </td>
                                        <td>{{ $pendapatan['tanggal'] }}</td>
                                        <td>{{ $pendapatan['keterangan'] }}</td>
                                        <td>{{ $pendapatan['metode'] }}</td>
                                        <td class="currency">{{ $pendapatan['jumlah'] }}</td>
                                    </tr>
                                    <?php $i++; $jumlahPendapatan += $pendapatan['jumlah'] ?>
                                    @endforeach
                                    <tr>
                                        <td colspan="4" align="right">Jumlah</td>
                                        <td class="currency">{{ $jumlahPendapatan }}</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </center>
                    </div>
                    <div class="table-responsive">
                        <center>
                            <h5 id="titlePengeluaran">Transaksi Pengeluaran Kost Bulan </h5>
                            <table class="table table-bordered table-striped table-hover" border="1" cellspacing="0" cellpadding="10">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Keterangan</th>
                                        <th>Metode</th>
                                        <th>Total Bayar</th>
                                    </tr>
                                </thead>
                                <tbody id="transaksiPengeluaran">
                                    <?php $i = 1; $jumlahPengeluaran = 0; ?>
                                    @if($transaksis[0]['pengeluaran'] != null)
                                    @foreach($transaksis[0]['pengeluaran'] as $pengeluaran)
                                    <tr>
                                        <td> {{ $i }} </td>
                                        <td>{{ $pengeluaran['tanggal'] }}</td>
                                        <td>{{ $pengeluaran['keterangan'] }}</td>
                                        <td>{{ $pengeluaran['metode'] }}</td>
                                        <td class="currency">{{ $pengeluaran['jumlah'] }}</td>
                                    </tr>
                                    <?php $i++; $jumlahPengeluaran += $pengeluaran['jumlah'];?>
                                    @endforeach
                                    <tr>
                                        <td colspan="4" align="right">Jumlah</td>
                                        <td class="currency">{{ $jumlahPengeluaran }}</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </center>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js-content')
<script type="text/javascript">

    function printData()
    {
       var divToPrint=document.getElementById("print");
       newWin= window.open("");
       newWin.document.write(divToPrint.innerHTML);
       newWin.print();
       newWin.close();
    }

    function currency(val) {
        var currency = 'Rp ' + val.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');

        return currency;
    }

    $(document).ready(function() {
        var tahun = $("#tahun").val();

        var bln   = $("#bulan option:selected").text();

        document.getElementById("titlePendapatan").innerHTML = 
                "Transaksi Pendapatan Kost Bulan " + bln + " " + tahun;

        document.getElementById("titlePengeluaran").innerHTML = 
                "Transaksi Pengeluaran Kost Bulan " + bln + " " + tahun;

        for (var i = 0 ; i < document.getElementsByClassName("currency").length; i++)
        {
            document.getElementsByClassName("currency")[i].innerHTML =
            currency(document.getElementsByClassName("currency")[i].innerHTML);
        }
    });

    function sortByKey(array, key) {
        return array.sort(function(a, b) {
            var x = a[key]; var y = b[key];
            return ((x < y) ? -1 : ((x > y) ? 1 : 0));
        });
    }

    $(document).on("change", "#bulan", function(){
        var bulan = $(this).val();
        var tahun = $("#tahun").val();

        var bln   = $("#bulan option:selected").text();

        $.ajax({
            type : "post",
            url  : "{{ route('owner.keu_gettransaksi') }}",
            data : { _token: "{{csrf_token()}}", bulan : bulan, tahun : tahun,
                   kodekost : "{{ Auth::user()->ownerkost[$indexKost]->kode_kost }}" },
            dataType : "json",
            success :function(data){

                document.getElementById("titlePendapatan").innerHTML = 
                "Transaksi Pendapatan Kost Bulan " + bln + " " + tahun;

                document.getElementById("transaksiPendapatan").innerHTML = "";
                
                if (data[0]['pendapatan'] != null)
                {
                    var index = 1;
                    var jmlPendapatan = 0;

                    var pendapatan = sortByKey(data[0]['pendapatan'], 'tanggal');

                    for (var i = 0; i < pendapatan.length; i++)
                    {
                        document.getElementById("transaksiPendapatan").innerHTML += 
                        "<tr>" +
                        "<td> " + index + " </td>" +
                        "<td>" + pendapatan[i]['tanggal'] + "</td>" +
                        "<td>" + pendapatan[i]['keterangan'] + "</td>" +
                        "<td>" + pendapatan[i]['metode'] + "</td>" +
                        "<td>" + currency(pendapatan[i]['jumlah']) + "</td>" +
                        "</tr>";
                        index++;
                        jmlPendapatan += parseInt(pendapatan[i]['jumlah']);
                    }

                    document.getElementById("transaksiPendapatan").innerHTML += 
                    "<tr>" +
                    "<td colspan='4' align='right'>Jumlah</td>" +
                    "<td>"+currency(jmlPendapatan)+"</td>" +
                    "</tr>";
                }

                document.getElementById("titlePengeluaran").innerHTML = 
                "Transaksi Pengeluaran Kost Bulan " + bln + " " + tahun;

                document.getElementById("transaksiPengeluaran").innerHTML = "";

                if (data[0]['pengeluaran'] != null)
                {
                    var index = 1;
                    var jmlPengeluaran = 0;
                    for (var i = 0; i < data[0]['pengeluaran'].length; i++)
                    {
                        document.getElementById("transaksiPengeluaran").innerHTML += 
                        "<tr>" +
                        "<td> " + index + " </td>" +
                        "<td>" + data[0]['pengeluaran'][i]['tanggal'] + "</td>" +
                        "<td>" + data[0]['pengeluaran'][i]['keterangan'] + "</td>" +
                        "<td>" + data[0]['pengeluaran'][i]['metode'] + "</td>" +
                        "<td>" + currency(data[0]['pengeluaran'][i]['jumlah']) + "</td>" +
                        "</tr>";
                        index++;
                        jmlPengeluaran += parseInt(data[0]['pendapatan'][i]['jumlah']);
                    }
                    document.getElementById("transaksiPengeluaran").innerHTML += 
                    "<tr>" +
                    "<td colspan='4' align='right'>Jumlah</td>" +
                    "<td>"+currency(jmlPengeluaran)+"</td>" +
                    "</tr>";
                }

            },
            error : function(data){
                document.getElementById("titlePendapatan").innerHTML = "";
                document.getElementById("transaksiPendapatan").innerHTML = "";

                document.getElementById("titlePengeluaran").innerHTML = "";
                document.getElementById("transaksiPengeluaran").innerHTML = "";

                alert('Error mengambil data transaksi');
            }
        });
    });

    $(document).on("change", "#tahun", function(){
        var bulan = $("#bulan").val();
        var tahun = $(this).val();

        var bln   = $("#bulan option:selected").text();

        $.ajax({
            type : "post",
            url  : "{{ route('owner.keu_gettransaksi') }}",
            data : { _token: "{{csrf_token()}}", bulan : bulan, tahun : tahun,
                   kodekost : "{{ Auth::user()->ownerkost[$indexKost]->kode_kost }}" },
            dataType : "json",
            success :function(data){

                document.getElementById("titlePendapatan").innerHTML = 
                "Transaksi Pendapatan Kost Bulan " + bln + " " + tahun;

                document.getElementById("transaksiPendapatan").innerHTML = "";
                if (data[0]['pendapatan'] != null)
                {
                    var index = 1;
                    var jmlPendapatan = 0;

                    var pendapatan = sortByKey(data[0]['pendapatan'], 'tanggal');
                    
                    for (var i = 0; i < pendapatan.length; i++)
                    {
                        document.getElementById("transaksiPendapatan").innerHTML += 
                        "<tr>" +
                        "<td> " + index + " </td>" +
                        "<td>" + pendapatan[i]['tanggal'] + "</td>" +
                        "<td>" + pendapatan[i]['keterangan'] + "</td>" +
                        "<td>" + pendapatan[i]['metode'] + "</td>" +
                        "<td>" + currency(pendapatan[i]['jumlah']) + "</td>" +
                        "</tr>";
                        index++;
                        jmlPendapatan += parseInt(pendapatan[i]['jumlah']);
                    }

                    document.getElementById("transaksiPendapatan").innerHTML += 
                    "<tr>" +
                    "<td colspan='4' align='right'>Jumlah</td>" +
                    "<td>"+currency(jmlPendapatan)+"</td>" +
                    "</tr>";
                }

                document.getElementById("titlePengeluaran").innerHTML = 
                "Transaksi Pengeluaran Kost Bulan " + bln + " " + tahun;

                document.getElementById("transaksiPengeluaran").innerHTML = "";

                if (data[0]['pengeluaran'] != null)
                {
                    var index = 1;
                    var jmlPengeluaran = 0;
                    for (var i = 0; i < data[0]['pengeluaran'].length; i++)
                    {
                        document.getElementById("transaksiPengeluaran").innerHTML += 
                        "<tr>" +
                        "<td> " + index + " </td>" +
                        "<td>" + data[0]['pengeluaran'][i]['tanggal'] + "</td>" +
                        "<td>" + data[0]['pengeluaran'][i]['keterangan'] + "</td>" +
                        "<td>" + data[0]['pengeluaran'][i]['metode'] + "</td>" +
                        "<td>" + currency(data[0]['pengeluaran'][i]['jumlah']) + "</td>" +
                        "</tr>";
                        index++;
                        jmlPengeluaran += parseInt(data[0]['pengeluaran'][i]['jumlah']);
                    }
                    document.getElementById("transaksiPengeluaran").innerHTML += 
                    "<tr>" +
                    "<td colspan='4' align='right'>Jumlah</td>" +
                    "<td>"+currency(jmlPengeluaran)+"</td>" +
                    "</tr>";
                }

            },
            error : function(data){
                document.getElementById("titlePendapatan").innerHTML = "";
                document.getElementById("transaksiPendapatan").innerHTML = "";

                document.getElementById("titlePengeluaran").innerHTML = "";
                document.getElementById("transaksiPengeluaran").innerHTML = "";

                alert('Error mengambil data transaksi');
            }
        });
    });

</script>
@endsection

@include('layouts.footer')