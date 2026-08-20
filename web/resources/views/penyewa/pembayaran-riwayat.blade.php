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
                <h2>Table Riwayat</h2>
                @php
                $i = 1;
                @endphp
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>No. Kamar</th>
                                <th>Batas Bayar</th>
                                <th>Tgl. Membayar</th>
                                <th>Denda</th>
                                <th>Harga Bayar</th>
                                <th>Yang Dibayarkan</th>
                                <th>Metode Bayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dp['personalinfobayar']['historibayar'] as $d)
                            <tr>
                                <td>
                                    @php
                                    echo $i;
                                    @endphp
                                </td>
                                <td>{{ $d['nomor_kamar'] }}</td>
                                <td>{{ $d['tanggal_pembayaran'] }}</td>
                                <td>{{ $d['tanggal_bayar'] }}</td>
                                <td>{{ $d['denda'] }}</td>
                                <td>{{ $d['total_harga'] }}</td>
                                <td>{{ $d['total_bayar'] }}</td>
                                <td>{{ $d['metode'] }}</td>
                            </tr>
                            @php
                            $i++;
                            @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
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
</script>
@endsection

@include('layouts.footer')