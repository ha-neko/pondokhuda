@extends('layouts.header')
@extends('layouts.sidebar')

@section('content')
<div class="row clearfix">
    <h1>Test</h1>
</div>


<!-- MODAL -->
<div class="modal fade bg-light-green" style="transition: all 0.3s;" id="addKost" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <span class="text-center">
                    <h4 class="modal-title black" id="largeModalLabel" style="color: #666; font-family: Century Gothic;">DETAIL DATA KOST</h4>
                </span>
            </div>
            <div class="modal-body">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <form action="{{route('owner.kost-create')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group form-float">
                                <label class="form-label">Nama Kost</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" id="namakost" name="namakost" required>
                                </div>
                            </div>
                            <div class="form-group form-float">
                                <label class="form-label">Alamat Kost</label>
                                <div class="form-line">
                                    <input type="text" class="form-control" id="alamatkost" name="alamatkost" required>
                                </div>
                            </div>
                            <div class="form-group form-float">
                                <label class="form-label">Email Kost</label>
                                <div class="form-line">
                                    <input type="email" class="form-control" id="emailkost" name="emailkost" required>
                                </div>
                            </div>
                            <div class="form-group form-float">
                                <label class="form-label">Warna Utama</label>
                                <div class="form-group">
                                    <input type="hidden" id="primarycolor" name="primarycolor" value="#8BC34A">
                                    <input type="hidden" id="namecolor" name="namecolor" value="light-green">
                                    <span class="badge bg-light-green" style="cursor: pointer;" onclick="color('#8BC34A', 'light-green')">&nbsp;&nbsp;&nbsp;</span>
                                    <span class="badge bg-green" style="cursor: pointer;" onclick="color('#4CAF50', 'green')">&nbsp;&nbsp;&nbsp;</span>
                                    <span class="badge bg-red" style="cursor: pointer;" onclick="color('#F44336', 'red')">&nbsp;&nbsp;&nbsp;</span>
                                    <span class="badge bg-blue" style="cursor: pointer;" onclick="color('#2196F3', 'blue')">&nbsp;&nbsp;&nbsp;</span>
                                    <span class="badge bg-light-blue" style="cursor: pointer;" onclick="color('#03A9F4', 'light-blue')">&nbsp;&nbsp;&nbsp;</span>
                                    <span class="badge bg-black" style="cursor: pointer;" onclick="color('#000000', 'black')">&nbsp;&nbsp;&nbsp;</span>
                                    <span class="badge bg-purple" style="cursor: pointer;" onclick="color('#9C27B0', 'purple')">&nbsp;&nbsp;&nbsp;</span>
                                    <span class="badge bg-brown" style="cursor: pointer;" onclick="color('#795548', 'brown')">&nbsp;&nbsp;&nbsp;</span>
                                    <span class="badge bg-grey" style="cursor: pointer;" onclick="color('#9E9E9E', 'grey')">&nbsp;&nbsp;&nbsp;</span>
                                    <span class="badge bg-blue-grey" style="cursor: pointer;" onclick="color('#607D8B', 'blue-grey')">&nbsp;&nbsp;&nbsp;</span>
                                    <span class="badge bg-pink" style="cursor: pointer;" onclick="color('#E91E63', 'pink')">&nbsp;&nbsp;&nbsp;</span>
                                    <span class="badge bg-deep-purple" style="cursor: pointer;" onclick="color('#673AB7', 'deep-purple')">&nbsp;&nbsp;&nbsp;</span>
                                    <span class="badge bg-indigo" style="cursor: pointer;" onclick="color('#3F51B5', 'indigo')">&nbsp;&nbsp;&nbsp;</span>
                                    <span class="badge bg-cyan" style="cursor: pointer;" onclick="color('#00BCD4', 'cyan')">&nbsp;&nbsp;&nbsp;</span>
                                    <span class="badge bg-teal" style="cursor: pointer;" onclick="color('#009688', 'teal')">&nbsp;&nbsp;&nbsp;</span>
                                    <span class="badge bg-lime" style="cursor: pointer;" onclick="color('#CDDC39', 'lime')">&nbsp;&nbsp;&nbsp;</span>
                                    <span class="badge bg-amber" style="cursor: pointer;" onclick="color('#FFC107', 'amber')">&nbsp;&nbsp;&nbsp;</span>
                                    <span class="badge bg-orange" style="cursor: pointer;" onclick="color('#FF9800', 'orange')">&nbsp;&nbsp;&nbsp;</span>
                                    <span class="badge bg-deep-orange" style="cursor: pointer;" onclick="color('#FF5722', 'deep-orange')">&nbsp;&nbsp;&nbsp;</span>

                                </div>
                            </div>
                            <span class="right">
                                <button type="submit" class="btn btn-link waves-effect">TAMBAH KOST</button>  
                                <button type="button" class="btn btn-link waves-effect" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">LOGOUT</button>
                        </form>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                            </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- #END# MODAL -->
@endsection

@section('js-content')

<script type="text/javascript">
    function color(hex, name) {
        document.getElementById("primarycolor").value = hex;
        document.getElementById("namecolor").value = name;
        document.getElementById("addKost").className = "modal fade bg-" + name + " in";
    }

    $(function () {
        $('#addKost').modal({
          backdrop: 'static',
          keyboard: false
        });
    });
    
</script>

@endsection

@extends('layouts.footer')