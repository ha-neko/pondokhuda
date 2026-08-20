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
            <div class="header">
                <h2>Informasi Sewa</h2>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped js-basic-example dataTable">
                        <thead>
                            <tr>
                                <th>No. Kamar</th>
                                <th>Nama Penyewa</th>
                                <th>Tanggal Jatuh Tempo</th>
                                <th>Sisa Hari</th>
                                <th>Besar Pembayaran</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($info['info'] != null)
                                @for($i = 0; $i < count($info['info']); ++$i)
                                    @if($i == 0 )
                                        @if(count($info['info']) > 1)
                                        @if($info['info'][$i]['nokamar'] == $info['info'][$i + 1]['nokamar'])
                                                <tr>
                                                    <td>{{ $info['info'][$i]['nokamar'] }}</td>
                                                    <td>
                                                        {{ $info['info'][$i]['namapenyewa'].', '.$info['info'][$i+1]['namapenyewa'] }}
                                                    </td>
                                                    <td>{{ $info['info'][$i]['nextbayar'] }}</td>
                                                    <td>{{ $info['info'][$i]['sisahari'] }} Hari</td>
                                                    <td>
                                                        Rp&nbsp;
                                                        @if($info['info'][$i]['sisabayar'] != "0")
                                                            {{ $info['info'][$i]['sisabayar'] }}
                                                        @else
                                                            @php
                                                            echo intval(
                                                            $info['info'][$i]['hargaperbulan']) +
                                                            intval($info['info'][$i]['denda'])
                                                            @endphp
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a class="btn btn-primary" onclick="getDetailInfoSewa('{{ $info["info"][$i]["id"] }}');" href="javascript:void(0);" data-toggle="modal" data-target="#detailInfoSewa"> Detail </a>
                                                    </td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td>{{ $info['info'][$i]['nokamar'] }}</td>
                                                    <td>
                                                        {{ $info['info'][$i]['namapenyewa'] }}
                                                    </td>
                                                    <td>{{ $info['info'][$i]['nextbayar'] }}</td>
                                                    <td>{{ $info['info'][$i]['sisahari'] }} Hari</td>
                                                    <td>
                                                        Rp&nbsp;
                                                        @if($info['info'][$i]['sisabayar'] != "0")
                                                            {{ $info['info'][$i]['sisabayar'] }}
                                                        @else
                                                            @php
                                                            echo intval(
                                                            $info['info'][$i]['hargaperbulan']) +
                                                            intval($info['info'][$i]['denda'])
                                                            @endphp
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a class="btn btn-primary" onclick="getDetailInfoSewa('{{ $info["info"][$i]["id"] }}');" href="javascript:void(0);" data-toggle="modal" data-target="#detailInfoSewa"> Detail </a>
                                                    </td>
                                                </tr>
                                            @endif
                                        @else
                                            <tr>
                                                <td>{{ $info['info'][$i]['nokamar'] }}</td>
                                                <td>
                                                    {{ $info['info'][$i]['namapenyewa'] }}
                                                </td>
                                                <td>{{ $info['info'][$i]['nextbayar'] }}</td>
                                                <td>{{ $info['info'][$i]['sisahari'] }} Hari</td>
                                                <td>
                                                    Rp&nbsp;
                                                    @if($info['info'][$i]['sisabayar'] != "0")
                                                        {{ $info['info'][$i]['sisabayar'] }}
                                                    @else
                                                        @php
                                                        echo intval(
                                                        $info['info'][$i]['hargaperbulan']) +
                                                        intval($info['info'][$i]['denda'])
                                                        @endphp
                                                    @endif
                                                </td>
                                                <td>
                                                    <a class="btn btn-primary" onclick="getDetailInfoSewa('{{ $info["info"][$i]["id"] }}');" href="javascript:void(0);" data-toggle="modal" data-target="#detailInfoSewa"> Detail </a>
                                                </td>
                                            </tr>
                                        @endif
                                    @else
                                        @if($i == count($info['info']) - 1)
                                            @if($info['info'][$i]['nokamar'] ==
                                            $info['info'][$i - 1]['nokamar'])
                                            @else
                                                <tr>
                                                    <td>{{ $info['info'][$i]['nokamar'] }}</td>
                                                    <td>
                                                        {{ $info['info'][$i]['namapenyewa'] }}
                                                    </td>
                                                    <td>{{ $info['info'][$i]['nextbayar'] }}</td>
                                                    <td>{{ $info['info'][$i]['sisahari'] }} Hari</td>
                                                    <td>
                                                        Rp&nbsp;
                                                        @if($info['info'][$i]['sisabayar'] != "0")
                                                            {{ $info['info'][$i]['sisabayar'] }}
                                                        @else
                                                            @php
                                                            echo intval(
                                                            $info['info'][$i]['hargaperbulan']) +
                                                            intval($info['info'][$i]['denda'])
                                                            @endphp
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a class="btn btn-primary" onclick="getDetailInfoSewa('{{ $info["info"][$i]["id"] }}');" href="javascript:void(0);" data-toggle="modal" data-target="#detailInfoSewa"> Detail </a>
                                                    </td>
                                                </tr>
                                            @endif
                                        @else
                                            @if($info['info'][$i]['nokamar'] == $info['info'][$i + 1]['nokamar'])
                                                <tr>
                                                    <td>{{ $info['info'][$i]['nokamar'] }}</td>
                                                    <td>
                                                        {{ $info['info'][$i]['namapenyewa'].', '.$info['info'][$i+1]['namapenyewa'] }}
                                                    </td>
                                                    <td>{{ $info['info'][$i]['nextbayar'] }}</td>
                                                    <td>{{ $info['info'][$i]['sisahari'] }} Hari</td>
                                                    <td>
                                                        Rp&nbsp;
                                                        @if($info['info'][$i]['sisabayar'] != "0")
                                                            {{ $info['info'][$i]['sisabayar'] }}
                                                        @else
                                                            @php
                                                            echo intval(
                                                            $info['info'][$i]['hargaperbulan']) +
                                                            intval($info['info'][$i]['denda'])
                                                            @endphp
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a class="btn btn-primary" onclick="getDetailInfoSewa('{{ $info["info"][$i]["id"] }}');" href="javascript:void(0);" data-toggle="modal" data-target="#detailInfoSewa"> Detail </a>
                                                    </td>
                                                </tr>
                                            @elseif($info['info'][$i]['nokamar'] ==
                                            $info['info'][$i - 1]['nokamar'])
                                            @else
                                                <tr>
                                                    <td>{{ $info['info'][$i]['nokamar'] }}</td>
                                                    <td>
                                                        {{ $info['info'][$i]['namapenyewa'] }}
                                                    </td>
                                                    <td>{{ $info['info'][$i]['nextbayar'] }}</td>
                                                    <td>{{ $info['info'][$i]['sisahari'] }} Hari</td>
                                                    <td>
                                                        Rp&nbsp;
                                                        @if($info['info'][$i]['sisabayar'] != "0")
                                                            {{ $info['info'][$i]['sisabayar'] }}
                                                        @else
                                                            @php
                                                            echo intval(
                                                            $info['info'][$i]['hargaperbulan']) +
                                                            intval($info['info'][$i]['denda'])
                                                            @endphp
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a class="btn btn-primary" onclick="getDetailInfoSewa('{{ $info["info"][$i]["id"] }}');" href="javascript:void(0);" data-toggle="modal" data-target="#detailInfoSewa"> Detail </a>
                                                    </td>
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
<!-- #END# Widgets -->

<!-- MODAL DETAIL -->
<div class="modal fade" id="detailInfoSewa" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-col-{{Auth::user()->ownerkost[$indexKost]['kost']['name_color']}}"">
            <div class="modal-header">
                <h4 class="modal-title" id="largeModalLabel">DETAIL INFORMASI SEWA</h4>
            </div>
            <div class="modal-body">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header">
                            </div>
                            <div class="body">
                                <div class="form-group form-float">
                                    <label class="form-label">Nomor Kamar</label>
                                    <div class="form-line">
                                        <input type="text" class="form-control" id="nokamar" name="nokamar" readonly="true">
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <label class="form-label">Nama Penyewa</label>
                                    <div class="form-line">
                                        <input type="text" class="form-control" id="namapenyewa" name="namapenyewa" readonly="true">
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <label class="form-label">Periode Sewa</label>
                                    <div class="form-line">
                                        <input type="text" class="form-control" id="periode_sewa" name="periode_sewa" readonly="true">
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <label class="form-label">Tanggal Jatuh Tempo</label>
                                    <div class="form-line">
                                        <input type="text" class="form-control" id="tgl_pembayaran_next" name="tgl_pembayaran_next" readonly="true">
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <label class="form-label">Sisa Hari</label>
                                    <div class="form-line">
                                        <input type="text" class="form-control" id="sisa_hari" name="sisa_hari" readonly="true">
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <label class="form-label">Harga Perbulan</label>
                                    <div class="form-line">
                                        <input type="text" class="form-control" id="harga_perbulan" name="harga_perbulan" readonly="true">
                                    </div>
                                </div>
                                <div id="lunas">
                                    <div class="form-group form-float">
                                        <label class="form-label">Denda</label>
                                        <div class="form-line">
                                            <input type="text" class="form-control" id="denda" name="denda" readonly="true">
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <label class="form-label">Besar Pembayaran</label>
                                        <div class="form-line">
                                            <input type="text" class="form-control" id="besar_bayar" name="besar_bayar" readonly="true">
                                        </div>
                                    </div>
                                </div>
                                <div id="hutang">
                                    <div class="form-group form-float">
                                        <label class="form-label">Denda Sebelumnya</label>
                                        <div class="form-line">
                                            <input type="text" class="form-control" id="denda_prev" name="denda_prev" readonly="true">
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <label class="form-label">Diskon Sebelumnya</label>
                                        <div class="form-line">
                                            <input type="text" class="form-control" id="diskon" name="diskon" readonly="true">
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <label class="form-label">Total Harga</label>
                                        <div class="form-line">
                                            <input type="text" class="form-control" id="total_harga" name="total_harga" readonly="true">
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <label class="form-label">Total Dibayarkan</label>
                                        <div class="form-line">
                                            <input type="text" class="form-control" id="total_dibayar" name="total_dibayar" readonly="true">
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <label class="form-label">Besar Sisa Pembayaran</label>
                                        <div class="form-line">
                                            <input type="text" class="form-control" id="sisa_bayar" name="sisa_bayar" readonly="true">
                                        </div>
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
    </div>
</div>
<!-- #END# MODAL DETAIL -->

@endsection

@section('js-content')

<script type="text/javascript">

    $(document).ready(function() {
        $('.js-basic-example').DataTable({
            // untuk sorting angka terus huruf
            columnDefs: [
                { type: 'natural', targets: 0 }
            ],
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

    function getDetailInfoSewa(kode) {
        $.ajax({
            type : "post",
            url  : "{{ route('owner.informasisewa-detail') }}",
            data : { _token: "{{csrf_token()}}", kode : kode },
            dataType : "json",
            success :function(data){
                document.getElementById("lunas").style.display = "";
                document.getElementById("hutang").style.display = "";

                document.getElementById("nokamar").value = data['informasisewa'][0]['nokamar'];
                if (data['informasisewa'].length > 1)
                {
                    document.getElementById("namapenyewa").value =
                     data['informasisewa'][0]['namapenyewa'] + ", " + data['informasisewa'][1]['namapenyewa'];
                }
                else
                {
                    document.getElementById("namapenyewa").value = data['informasisewa'][0]['namapenyewa'];
                }
                document.getElementById("periode_sewa").value = data['informasisewa'][0]['periodesewa'];
                document.getElementById("tgl_pembayaran_next").value = data['informasisewa'][0]['nextbayar'];
                document.getElementById("sisa_hari").value = data['informasisewa'][0]['sisahari'];
                document.getElementById("harga_perbulan").value = data['informasisewa'][0]['hargaperbulan'];

                if (data['informasisewa'][0]['status'] == 'Lunas')
                {
                    document.getElementById("hutang").style.display = "none";
                    document.getElementById("denda").value = data['informasisewa'][0]['denda'];
                    document.getElementById("besar_bayar").value =
                    String(parseInt(data['informasisewa'][0]['hargaperbulan']) + parseInt(data['informasisewa'][0]['denda']));
                }
                else
                {
                    document.getElementById("lunas").style.display = "none";
                    document.getElementById("denda_prev").value = data['informasisewa'][0]['dendaprev'];
                    document.getElementById("diskon").value = data['informasisewa'][0]['diskonprev'];
                    document.getElementById("total_harga").value = data['informasisewa'][0]['totalhargaprev'];
                    document.getElementById("total_dibayar").value = data['informasisewa'][0]['totalbayarprev'];
                    document.getElementById("sisa_bayar").value = data['informasisewa'][0]['sisabayar'];
                }
            },
            error : function(data){
                alert('Error mengambil data detail admin, Error: ' + data);
                document.getElementById("lunas").style.display = "none"
                document.getElementById("hutang").style.display = "none"
            }
        });
    }
</script>
@endsection

@include('layouts.footer')