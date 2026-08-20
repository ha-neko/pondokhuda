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
                    List Data Penyewa
                </h2>
                <ul class="header-dropdown m-r--6">
                    <li class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons">more_vert</i>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <li>
                                <a href="{{ route('owner.tambahpenyewa') }}">
                                    <i class="material-icons">add</i> <span class="icon-name">Penyewa Baru</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('owner.pindahpenyewa') }}">
                                    <i class="material-icons">directions_run</i> <span class="icon-name">Pindah Kamar</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('owner.berhentisewa') }}">
                                    <i class="material-icons">block</i> <span class="icon-name">Berhenti Sewa</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="body">
                <div class="table-responsive">
                	<table id="dataTable" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No Kamar</th>
                                <th>Nama Penyewa</th>
                                <th>Kirim Ulang Email</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($penyewas['datapenyewa'] != null)
                            @foreach($penyewas['datapenyewa'] as $penyewa)
                            <tr>
                                <td>{{ $penyewa['nokamar'] }}</td>
                                <td>{{ $penyewa['nama'] }}</td>
                                <td>
                                    <a class="btn btn-danger" onclick="load(); resendEmail('{{ $penyewa["kode"] }}');" href="javascript:void(0);">Email Pendaftaran</a>
                                    <a class="btn btn-success" onclick="load(); resendKwitansi('{{ $penyewa["kode"] }}');" href="javascript:void(0);">Kwitansi Pembayaran Terakhir</a>
                                </td>
                                <td>
                                    <a href="{{ route('owner.editpenyewa', $penyewa['kode']) }}">
                                        <button type="button" class="btn btn-warning waves-effect">
                                            Edit
                                        </button>
                                    </a>
                                    <a class="btn btn-primary" onclick="getDetailPenyewa('{{ $penyewa["kode"] }}');" href="javascript:void(0);" data-toggle="modal" data-target="#detailPenyewa">Detail</a>
                                    <!-- <a class="btn btn-primary" href="https://wa.me/6281222219461/?text=">
                                        <i class="material-icons">chat</i>
                                    </a> -->
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL -->
<div class="modal fade" id="detailPenyewa" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-col-{{Auth::user()->ownerkost[$indexKost]['kost']['name_color']}}">
            <div class="modal-header">
            </div>
            <div class="modal-body">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header">
                                <h2>DETAIL DATA PENYEWA</h2>
                            </div>
                            <div class="body">
                                <center>
                                    <div class="form-group form-float">
                                        <div id="img-foto-detail" style="background-image: url(''); width: 150px; height: 150px; background-size: cover; background-repeat: no-repeat; background-position: 50% 50%; border-radius: 100px; border: 5px solid #d4d4d6;"></div>
                                    </div>
                                </center>
                                <div class="form-group form-float">
                                    <label class="form-label">Kode Penyewa</label>
                                    <div class="form-line">
                                        <input type="text" class="form-control" id="kode-detail" name="kode-detail" readonly="true">
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <label class="form-label">Nama Penyewa</label>
                                    <div class="form-line">
                                        <input type="text" class="form-control" id="nama-detail" name="nama-detail" readonly="true">
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <label class="form-label">Nomor Kamar</label>
                                    <div class="form-line">
                                        <input type="number" class="form-control" id="nomorkamar-detail" name="nomorkamar-detail" readonly="true">
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <label class="form-label">Tanggal Pembayaran Selanjutnya</label>
                                    <div class="form-line">
                                        <input type="text" class="form-control" id="tgl_pembayaran_next" name="tgl_pembayaran_prev" readonly="true">
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <label class="form-label">Hari Menuju Bayar</label>
                                    <div class="form-line">
                                        <input type="text" class="form-control" id="hari_menuju_bayar" name="hari_menuju_bayar" readonly="true">
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <label class="form-label">Besar Pembayaran</label>
                                    <div class="form-line">
                                        <input type="text" class="form-control" id="besar_pembayaran" name="besar_pembayaran" readonly="true">
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <label class="form-label">Tanggal Terakhir Bayar</label>
                                    <div class="form-line">
                                        <input type="text" class="form-control" id="tgl_bayar_terakhir" name="hari_menuju_bayar" readonly="true">
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <label class="form-label">Jumlah Pembayaran Terakhir</label>
                                    <div class="form-line">
                                        <input type="text" class="form-control" id="jml_bayar_prev" name="hari_menuju_bayar" readonly="true">
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
    </section>
    </div>
</div>
<!-- #END# MODAL -->

<div class="loading" id="loading">Loading&#8230;</div>
@endsection

@section('js-content')

<script type="text/javascript">

    $(function () {
        $('#dataTable').DataTable({
            // untuk sorting angka terus huruf
            columnDefs: [
                { type: 'natural', targets: 0 }
            ],
            responsive: true
        });
    });

    function currency(val) {
        var currency = 'Rp ' + val.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');

        return currency;
    }

    function getDetailPenyewa(kode) {
        $.ajax({
            type : "post",
            url  : "{{ route('owner.penyewa-detail') }}",
            data : { _token: "{{csrf_token()}}", kode : kode },
            dataType : "json",
            success :function(data){
                
                document.getElementById("img-foto-detail").style.backgroundImage = "url('" + data['penyewa'][0]['urlfoto'] + "')";
                document.getElementById("kode-detail").value = data['penyewa'][0]['kode'];
                document.getElementById("nama-detail").value = data['penyewa'][0]['nama'];
                document.getElementById("nomorkamar-detail").value = data['penyewa'][0]['nokamar'];
                document.getElementById("tgl_pembayaran_next").value = data['penyewa'][0]['nextbayar'];
                document.getElementById("hari_menuju_bayar").value = data['penyewa'][0]['harimenujubayar'];
                if(data['penyewa'][0]['sisabayarprev'] != 0)
                {
                    document.getElementById("besar_pembayaran").value = currency(data['penyewa'][0]['sisabayarprev']);
                }
                else
                {
                    document.getElementById("besar_pembayaran").value =

                    currency(String(parseInt(data['penyewa'][0]['hargaperbulan']) + parseInt(data['penyewa'][0]['denda'])));
                }
                document.getElementById("tgl_bayar_terakhir").value = data['penyewa'][0]['tglbayarprev'];
                document.getElementById("jml_bayar_prev").value = currency(data['penyewa'][0]['bayarprev']);
            },
            error : function(data){
                alert('Error mengambil data detail kamar, Error: ' + JSON.stringify(data));
            }
        });
    }

    function resendEmail(kode) {

        $.ajax({
            type : "post",
            url  : "{{ route('owner.penyewa-resend_email_daftar') }}",
            data : { _token: "{{csrf_token()}}", kode : kode },
            dataType : "json",
            success :function(data){
                showEmailMessage();
            },
            error : function(data){
                failEmailMessage();
            }
        });

        document.getElementById("loading").style.zIndex = "-999";
    }

    function resendKwitansi(kode) {

        $.ajax({
            type : "post",
            url  : "{{ route('owner.penyewa-resend_kwitansi') }}",
            data : { _token: "{{csrf_token()}}", kode : kode },
            dataType : "json",
            success :function(data){
                showKwitansiMessage();
            },
            error : function(data){
                failKwitansiMessage();
            }
        });

        document.getElementById("loading").style.zIndex = "-999";
    }

    function load()
    {
        document.getElementById("loading").style.zIndex = "999";
        document.getElementById("loading").style.opacity = "0.8";
        showLoadingMessage();
    }

    function showLoadingMessage() {
        swal({
            title: "Harap tunggu",
            text: "Sedang mengirim email",
            type: "info",
            showConfirmButton: false
        });
    }

    function showEmailMessage() {
        swal("Email Pendaftaran Sudah Dikirim Ulang",
        "Silahkan cek kembali email untuk melihat kode dan pin", "success");
    }

    function failEmailMessage() {
        swal("Email Pendaftaran Gagal Dikirim",
        "Harap hubungi pihak developer untuk detail lebih lanjut", "error");
    }

    function showKwitansiMessage() {
        swal("Kwitansi Sudah Dikirim Ulang",
        "Silahkan cek email untuk melihat kwitansi pembayaran terakhir", "success");
    }

    function failKwitansiMessage() {
        swal("Kwitansi Gagal Dikirim",
        "Harap hubungi pihak developer untuk detail lebih lanjut", "error");
    }

</script>

@endsection

@include('layouts.footer')