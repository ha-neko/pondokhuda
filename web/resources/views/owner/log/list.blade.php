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
                    List Log Admin
                </h2>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table id="dataTable" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Waktu Akses</th>
                                <th>Nama Admin</th>
                                <th>Aktifitas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($logs['logadmin'] != null)
                                @foreach($logs['logadmin'] as $log)
                                <tr>
                                    <td>{{$log['waktu_akses']}}</td>
                                    <td>{{$log['nama']}}</td>
                                    <td>{{$log['aktifitas']}}</td>
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

@endsection

@section('js-content')

<script type="text/javascript">
    $(function () {
        $('#dataTable').DataTable({
            // untuk sorting angka terus huruf
            order: [[ 0, "desc" ]],
            columnDefs: [
                { type: 'date-euro', targets: 0, }
            ],
            responsive: true
        });
    });
</script>

@endsection

@include('layouts.footer')