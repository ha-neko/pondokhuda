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
                    List Data Kamar
                </h2>
                <ul class="header-dropdown m-r--5">
                    <li class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons">more_vert</i>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <li><a href="{{ route('owner.kamar-add') }}">Tambah Kamar</a></li>
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
                                <th>Status Disewakan</th>
                                <th>Status Terisi</th>
                                <th>Harga Kamar (Rp)</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($kamars['datakamar'] != null)
                                @foreach($kamars['datakamar'] as $kamar)
                                <tr>
                                    <td>{{ $kamar['nokamar'] }}</td>
                                    <td>{{ $kamar['statuskamar'] }}</td>
                                    @if($kamar['hargadisewakan'] != "")
                                    <td>Terisi</td>
                                    <td class="currency">{{ $kamar['hargadisewakan'] }}</td>
                                    @else
                                    <td>Kosong</td>
                                    <td class="currency">{{ $kamar['harga'] }}</td>
                                    @endif
                                    <td>
                                        <a class="btn btn-primary" onclick="getDetailKamar({{ $kamar['kode'] }});" data-toggle="modal" data-target="#detailKamar"> Detail </a>
                                        <a class="btn btn-warning" href="{{ route('owner.kamar-edit', $kamar['kode']) }}"> Ubah </a>
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

<!-- MODAL DETAIL -->
<div class="modal fade" id="detailKamar" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-col-{{Auth::user()->ownerkost[$indexKost]['kost']['name_color']}}">
            <div class="modal-header">
            </div>
            <div class="modal-body">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header">
                                <h2>DETAIL DATA KAMAR</h2>
                            </div>
                            <div class="body">
                                <div class="form-group form-float">
                                    <label class="form-label">Nomor Kamar</label>
                                    <div class="form-line">
                                        <input value="" type="text" class="form-control" id="nomorkamar-detail" name="nomorkamar-detail" readonly="true">
                                    </div>
                                </div>
                                <div id="gasewa">
                                    <div class="form-group form-float">
                                        <div class="form-group">
                                            <input type="radio" name="statuskamar-detail" id="sewa-detail" class="radio-col-{{Auth::user()->ownerkost[$indexKost]['kost']['name_color']}} with-gap" value="Untuk Sewa" disabled="true">
                                            <label for="sewa-detail">Untuk Sewa</label>

                                            <input type="radio" name="statuskamar-detail" id="bukansewa-detail" class="radio-col-{{Auth::user()->ownerkost[$indexKost]['kost']['name_color']}} with-gap" value="Bukan Untuk Sewa" disabled="true">
                                            <label for="bukansewa-detail">Bukan Untuk Sewa</label>
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <label class="form-label">Harga Kamar (Rp)</label>
                                        <div class="form-line">
                                            <input value="" type="text" class="form-control" id="hargakamar-detail" name="hargakamar-detail" readonly="true">
                                        </div>
                                    </div>
                                </div>
                                <div id="disewa">
                                    <div class="form-group form-float">
                                        <label class="form-label">Harga Kamar (Rp)</label>
                                        <div class="form-line">
                                            <input value="0" type="text" class="form-control" id="hargadisewakan-detail" name="hargadisewakan-detail" readonly="true" >
                                        </div>
                                    </div>
                                    <div class="form-group form-float">
                                        <label class="form-label">Jumlah Penyewa Saat Ini</label>
                                        <div class="form-line">
                                            <input value="" type="number" class="form-control" id="jumlahpenyewa-detail" name="jumlahpenyewa-detail" required readonly="true">
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
    </section>
    </div>
</div>
<!-- #END# MODAL DETAIL -->

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
        var currency = "";

        if (val != "" && val != null)
        {
            currency = 'Rp ' + val.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
        }
        

        return currency;
    }

    $( document ).ready(function() {
        for (var i = 0 ; i < document.getElementsByClassName("currency").length; i++)
        {
            document.getElementsByClassName("currency")[i].innerHTML =
            currency(document.getElementsByClassName("currency")[i].innerHTML);
        }
    });

    function getDetailKamar(kode) {
        $.ajax({
            type : "post",
            url  : "{{ route('owner.kamar-detail') }}",
            data : { _token: "{{csrf_token()}}", kode : kode },
            dataType : "json",
            success :function(data){
                document.getElementById("gasewa").style.display = "";
                document.getElementById("disewa").style.display = "";

                document.getElementById("nomorkamar-detail").value = data['datakamar'][0]['nokamar'];
                if (data['datakamar'][0]['jmlpenyewa'] > 0)
                {
                    document.getElementById("gasewa").style.display = "none";
                    document.getElementById("hargadisewakan-detail").value = currency(data['datakamar'][0]['hargadisewakan']);
                    document.getElementById("jumlahpenyewa-detail").value = data['datakamar'][0]['jmlpenyewa'];
                }
                else
                {
                    document.getElementById("disewa").style.display = "none";
                    $("input[name=statuskamar-detail][value='"+data['datakamar'][0]['statuskamar']+"']").prop('checked',true);
                    document.getElementById("hargakamar-detail").value = currency(data['datakamar'][0]['harga']);
                }
            },
            error : function(data){
                alert('Error mengambil data detail kamar, Error: ' + JSON.stringify(data));
            }
        });
    }

</script>

@endsection

@include('layouts.footer')