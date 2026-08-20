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

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card bg-light-green">
            <div class="header">
                <a href="{{ route('admin.penyewa') }}">
                <button class="btn btn-default waves-effect" type="button"><i class="material-icons">keyboard_arrow_left</i> <span class="icon-name">Kembali</span></button>
                </a>
            </div>
            <div class="body">
                <fieldset>
                	@foreach($penyewa['datapenyewa'] as $p)
                	@if($p['kode'] == $kode)
	                <div class="row clearfix">
	                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	                        <div class="card">
	                            <div class="body">
            						<form action="{{ route('admin.updatepenyewa', $kode) }}" method="post">
            						@csrf
	                                
	                                <button class="btn bg-light-green waves-effect" type="submit"><i class="material-icons">file_upload</i> <span class="icon-name">Pindah</span></button>
            						</form>
	                            </div>
	                        </div>
	                    </div>
	                </div>
                	@endif
                	@endforeach
                </fieldset>
            </div>
        </div>
    </div>
</div>

@endsection
@section('js-content')
<script type="text/javascript">

// window.onload = function() {
//     document.getElementById("cboxProv").selectedIndex = 0;
// }
</script>
<!-- JQuery Steps Plugin Js -->
<script src="{{{ URL::asset('adminbsb/plugins/jquery-steps/jquery.steps.js') }}}"></script>

<!-- Sweet Alert Plugin Js -->
<script src="{{{ URL::asset('adminbsb/plugins/sweetalert/sweetalert.min.js') }}}"></script>

<!-- Custom JS -->
<script src="{{{ URL::asset('adminbsb/js/pages/forms/form-wizard.js') }}}"></script>
<script src="{{{ URL::asset('adminbsb/js/pages/forms/basic-form-elements.js') }}}"></script>

<!-- Autosize Plugin Js -->
<script src="{{{ URL::asset('adminbsb/plugins/autosize/autosize.js') }}}"></script>

<!-- Moment Plugin Js -->
<script src="{{{ URL::asset('adminbsb/plugins/momentjs/moment.js') }}}"></script>

<!-- Bootstrap Material Datetime Picker Plugin Js -->
<script src="{{{ URL::asset('adminbsb/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}}"></script>

<!-- Dropzone Plugin Js -->
<script src="{{{ URL::asset('adminbsb/plugins/dropzone/dropzone.js') }}}"></script>

<!-- Bootstrap Colorpicker Js -->
<script src="{{{ URL::asset('adminbsb/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js') }}}"></script>

<script src="{{{ URL::asset('adminbsb/js/pages/forms/advanced-form-elements.js') }}}"></script>
@endsection
@include('layouts.footer')