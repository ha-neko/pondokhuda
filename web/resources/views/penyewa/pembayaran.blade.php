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
            <div class="header bg-{{Auth::user()->kost->name_color}}">
                <center>
                    <text style="font-size: 18px;">{{ $bio['resumepembayaran']['line1'] }}</text>
                    <br><br>
                    <text style="font-size: 30px;">{{ $bio['resumepembayaran']['line2'] }}</text>
                    <br>
                    <text style="font-size: 15px;">{{ $bio['resumepembayaran']['line3'] }}</text>
                    <br><br>
                    <text style="font-size: 15px;">{{ $bio['resumepembayaran']['line4'] }}</text>
                    <br>
                    <text style="font-size: 15px;">{{ $bio['resumepembayaran']['line5'] }}</text>
                </center>
            </div>
            <div class="body">
                <center>
                <table width="100%">
                    <tr>
                        <td width="50%" valign="top" align="center">
                        	<text style="font-size: 18px;"><b>Terakhir Bayar</b></text>
                        	<br>
                        	<text style="font-size: 18px;">{{ $bio['personalinfobayar']['tanggalbayar'] }}</text>
                        	<br><br>
                        	<text style="font-size: 18px;"><b>Hutang</b></text>
                        	<br>
                        	<text style="font-size: 18px;">{{ $bio['personalinfobayar']['sisabayarsebelumnya'] }}</text>
                        </td>
                        <td width="50%" valign="top" align="center">
                        	<text style="font-size: 18px;"><b>Baru Dibayarkan</b></text>
                        	<br>
                        	<text style="font-size: 18px;">{{ $bio['personalinfobayar']['bayarsebelumnya'] }}</text>
                        	<br><br>
                        	<text style="font-size: 18px;"><b>Status Bayar</b></text>
                        	<br>
                        	<text style="font-size: 18px;">{{ $bio['personalinfobayar']['statusbayar'] }}</text>
                        </td>
                    </tr>
                </table>
                </center>
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

</script>

@endsection

@include('layouts.footer')