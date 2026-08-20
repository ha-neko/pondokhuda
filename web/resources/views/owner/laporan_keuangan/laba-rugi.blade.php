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
                    Laporan Laba Rugi Kost
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
                <div class="table-responsive" id="print">
                	<center>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th colspan="3" id="judul" class="text-center">
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="3">
                                        <span class="font-bold font-italic font-underline"> 
                                            PENDAPATAN SEWA KOST :
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Pendapatan</td>
                                    <td class="currency" id="pendapatan">{{$labarugis['pendapatan'][0]['pendapatankost']}}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Discount</td>
                                    <td class="currency" id="diskon">{{$labarugis['pendapatan'][0]['diskon']}}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><b>PENDAPATAN NETO KOST</b></td>
                                    <td></td>
                                    <td><b class="currency" id="totalneto">{{$labarugis['pendapatan'][0]['totalneto']}}</b></td>
                                </tr>
                                <tr>
                                    <td colspan="3" height="20px"></td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <span class="font-bold font-italic font-underline"> 
                                            PENDAPATAN LAIN-LAIN :
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Denda Telat Bayar</td>
                                    <td class="currency" id="denda">{{$labarugis['pendapatan'][0]['pendapatandenda']}}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Bayar Perhari Pindah Kost</td>
                                    <td class="currency" id="pindah">{{$labarugis['pendapatan'][0]['pendapatanpindah']}}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Pendapatan Lain - Lain</td>
                                    <td class="currency" id="pendapatanlain">{{$labarugis['pendapatan'][0]['pendapatanlainnya']}}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><b>JUMLAH PENDAPATAN LAIN-LAIN</b></td>
                                    <td></td>
                                    <td>
                                        <b class="currency" id="totalpendapatanlain">
                                            {{$labarugis['pendapatan'][0]['totallainlain']}}
                                        </b>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>PENDAPATAN BRUTO</b></td>
                                    <td></td>
                                    <td><b class="currency" id="totalpendapatan">{{$labarugis['pendapatan'][0]['total']}}</b></td>
                                </tr>
                                <tr>
                                    <td colspan="3" height="20px"></td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <span class="font-bold font-italic font-underline"> 
                                            BIAYA-BIAYA :
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Gaji & Tunjangan</td>
                                    <td class="currency" id="gajitunjangan">{{$labarugis['beban'][0]['gajitunjangan']}}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Biaya Perbaikan</td>
                                    <td class="currency" id="perbaikan">{{$labarugis['beban'][0]['perbaikan']}}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Biaya Tukang</td>
                                    <td class="currency" id="tukang">{{$labarugis['beban'][0]['tukang']}}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Biaya Listrik</td>
                                    <td class="currency" id="listrik">{{$labarugis['beban'][0]['listrik']}}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Biaya Listrik</td>
                                    <td class="currency" id="air">{{$labarugis['beban'][0]['air']}}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Biaya Sampah</td>
                                    <td class="currency" id="sampah">{{$labarugis['beban'][0]['sampah']}}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Biaya Iuran</td>
                                    <td class="currency" id="iuran">{{$labarugis['beban'][0]['iuran']}}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Biaya Internet</td>
                                    <td class="currency" id="internet">{{$labarugis['beban'][0]['internet']}}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Pajak</td>
                                    <td class="currency" id="pajak">{{$labarugis['beban'][0]['pajak']}}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Beban Lain-Lain</td>
                                    <td class="currency" id="bebanlain">{{$labarugis['beban'][0]['lainlain']}}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><b>JUMLAH BEBAN USAHA</b></td>
                                    <td></td>
                                    <td><b class="currency" id="totalbeban">{{$labarugis['beban'][0]['total']}}</b></td>
                                </tr>
                                <tr>
                                    <td colspan="3" height="20px"></td>
                                </tr>
                                <tr>
                                    <td><u><b>PENGHASILAN NETO</b></u></td>
                                    <td></td>
                                    <td>
                                        <u><b class="currency" id="penghasilanneto">
                                        {{$labarugis['pendapatan'][0]['total'] - $labarugis['beban'][0]['total']}}</b></u>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </center>
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
        newWin.document.write( "<html><head><title></title>" );
        newWin.document.write( "<style>" );
        newWin.document.write( "table { border: 1px solid #000; width: 100%; }" );
        newWin.document.write( "</style>" );
        newWin.document.write( "</head><body >" );
        newWin.document.write( "</body></html>" );
        //newWin.document.write(divToPrint.setAttribute("class", "table"));
        newWin.document.write(divToPrint.innerHTML);
        newWin.print();
        newWin.close();
    }

    function currency(val) {
        var currency = 'Rp ' + val.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');

        return currency;
    }

    $( document ).ready(function() {

        var lastday = function(y,m){
            return new Date(y, m +1, 0).getDate();
        }

        var tahun = $("#tahun").val();
        var bulan = $("#bulan").val();
        var tgl   = lastday(tahun, (bulan - 1));
        var bln   = $("#bulan option:selected").text();

        document.getElementById("judul").innerHTML = 
        "LAPORAN LABA RUGI <br> KOST <br> " + tgl + " " + bln + " " + tahun;

        for (var i = 0 ; i < document.getElementsByClassName("currency").length; i++)
        {
            document.getElementsByClassName("currency")[i].innerHTML =
            currency(document.getElementsByClassName("currency")[i].innerHTML);
        }
    });

    $(document).on("change", "#bulan", function(){

        var lastday = function(y,m){
            return new Date(y, m +1, 0).getDate();
        }
        var bulan = $(this).val();
        var tahun = $("#tahun").val();

        var tgl   = lastday(tahun, (bulan - 1));
        var bln   = $("#bulan option:selected").text();

        $.ajax({
            type : "post",
            url  : "{{ route('owner.keu_getlabarugi') }}",
            data : { _token: "{{csrf_token()}}", bulan : bulan, tahun : tahun,
                     kodekost : "{{ Auth::user()->ownerkost[$indexKost]->kode_kost }}" },
            dataType : "json",
            success :function(data){

                document.getElementById("judul").innerHTML =
                "LAPORAN LABA RUGI" +
                "<br>" +
                "KOST" +
                "<br>" +
                tgl + " " + bln + " " + tahun;

                document.getElementById("pendapatan").innerHTML = currency(data['pendapatan'][0]['pendapatankost']);
                document.getElementById("diskon").innerHTML = currency(data['pendapatan'][0]['diskon']);
                document.getElementById("totalneto").innerHTML = currency(data['pendapatan'][0]['totalneto']);
                document.getElementById("denda").innerHTML = currency(data['pendapatan'][0]['pendapatandenda']);
                document.getElementById("pindah").innerHTML = currency(data['pendapatan'][0]['pendapatanpindah']);
                document.getElementById("pendapatanlain").innerHTML = currency(data['pendapatan'][0]['pendapatanlainnya']);
                document.getElementById("totalpendapatanlain").innerHTML = currency(data['pendapatan'][0]['totallainlain']);
                document.getElementById("totalpendapatan").innerHTML = currency(data['pendapatan'][0]['total']);

                document.getElementById("gajitunjangan").innerHTML = currency(data['beban'][0]['gajitunjangan']);
                document.getElementById("perbaikan").innerHTML = currency(data['beban'][0]['perbaikan']);
                document.getElementById("tukang").innerHTML = currency(data['beban'][0]['tukang']);
                document.getElementById("listrik").innerHTML = currency(data['beban'][0]['listrik']);
                document.getElementById("air").innerHTML = currency(data['beban'][0]['air']);
                document.getElementById("sampah").innerHTML = currency(data['beban'][0]['sampah']);
                document.getElementById("iuran").innerHTML = currency(data['beban'][0]['iuran']);
                document.getElementById("internet").innerHTML = currency(data['beban'][0]['internet']);
                document.getElementById("pajak").innerHTML = currency(data['beban'][0]['pajak']);
                document.getElementById("bebanlain").innerHTML = currency(data['beban'][0]['lainlain']);
                document.getElementById("totalbeban").innerHTML = currency(data['beban'][0]['total']);

                document.getElementById("penghasilanneto").innerHTML = currency(data['pendapatan'][0]['total'] -
                                                                       data['beban'][0]['total']);
            },
            error : function(data){
                alert("Error");
                document.getElementById("judul").innerHTML = "";
            }
        });
    });

    $(document).on("change", "#tahun", function(){

        var lastday = function(y,m){
            return new Date(y, m +1, 0).getDate();
        }
        var bulan = $("#bulan").val();
        var tahun = $(this).val();

        var tgl   = lastday(tahun, (bulan - 1));
        var bln   = $("#bulan option:selected").text();

        $.ajax({
            type : "post",
            url  : "{{ route('owner.keu_getlabarugi') }}",
            data : { _token: "{{csrf_token()}}", bulan : bulan, tahun : tahun,
                     kodekost : "{{ Auth::user()->ownerkost[$indexKost]->kode_kost }}" },
            dataType : "json",
            success :function(data){

                document.getElementById("judul").innerHTML =
                "LAPORAN LABA RUGI" +
                "<br>" +
                "KOST" +
                "<br>" +
                tgl + " " + bln + " " + tahun;

                document.getElementById("pendapatan").innerHTML = currency(data['pendapatan'][0]['pendapatankost']);
                document.getElementById("diskon").innerHTML = currency(data['pendapatan'][0]['diskon']);
                document.getElementById("totalneto").innerHTML = currency(data['pendapatan'][0]['totalneto']);
                document.getElementById("denda").innerHTML = currency(data['pendapatan'][0]['pendapatandenda']);
                document.getElementById("pindah").innerHTML = currency(data['pendapatan'][0]['pendapatanpindah']);
                document.getElementById("pendapatanlain").innerHTML = currency(data['pendapatan'][0]['pendapatanlainnya']);
                document.getElementById("totalpendapatanlain").innerHTML = currency(data['pendapatan'][0]['totallainlain']);
                document.getElementById("totalpendapatan").innerHTML = currency(data['pendapatan'][0]['total']);

                document.getElementById("gajitunjangan").innerHTML = currency(data['beban'][0]['gajitunjangan']);
                document.getElementById("perbaikan").innerHTML = currency(data['beban'][0]['perbaikan']);
                document.getElementById("tukang").innerHTML = currency(data['beban'][0]['tukang']);
                document.getElementById("listrik").innerHTML = currency(data['beban'][0]['listrik']);
                document.getElementById("air").innerHTML = currency(data['beban'][0]['air']);
                document.getElementById("sampah").innerHTML = currency(data['beban'][0]['sampah']);
                document.getElementById("iuran").innerHTML = currency(data['beban'][0]['iuran']);
                document.getElementById("internet").innerHTML = currency(data['beban'][0]['internet']);
                document.getElementById("pajak").innerHTML = currency(data['beban'][0]['pajak']);
                document.getElementById("bebanlain").innerHTML = currency(data['beban'][0]['lainlain']);
                document.getElementById("totalbeban").innerHTML = currency(data['beban'][0]['total']);

                document.getElementById("penghasilanneto").innerHTML = currency(data['pendapatan'][0]['total'] -
                                                                       data['beban'][0]['total']);

            },
            error : function(data){
                alert("Error");
                document.getElementById("judul").innerHTML = "";
            }
        });
    });

</script>
@endsection

@include('layouts.footer')