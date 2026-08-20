<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
        @if(Auth::guard('super-owner')->check())
        <title> Si Juragan Kost | {{ $data['pageTitle'] }} </title>
        @else
        <title> {{ isset($appName) ? $appName : 'PondokHuda' }} | {{ $data['pageTitle'] }} </title>
        @endif
    @else
    <title> Si Juragan Kost | {{ $data['pageTitle'] }} </title>
    @endauth
    <!-- Favicon-->
    <link rel="icon" href="{{ asset('Assets/images/logo/default-logo-black.png') }}" type="image/png">

    <!-- Bootstrap Core Css -->
    <link href="{{{ URL::asset('adminbsb/plugins/bootstrap/css/bootstrap.css')}}}" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="{{{ URL::asset('adminbsb/plugins/node-waves/waves.css')}}}" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="{{{ URL::asset('adminbsb/plugins/animate-css/animate.css')}}}" rel="stylesheet" />

    <!-- Bootstrap Material Datetime Picker Css -->
    <link href="{{{ URL::asset('adminbsb/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}}" rel="stylesheet" />

    <!-- bootstrap-datetimepicker -->
    <link href="{{{ URL::asset('adminbsb/plugins/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css') }}}" rel="stylesheet">

    <!-- Wait Me Css -->
    <link href="{{{ URL::asset('adminbsb/plugins/waitme/waitMe.css') }}}" rel="stylesheet" />

    <!-- Bootstrap Select Css -->
    <!-- <link href="{{{ URL::asset('adminbsb/plugins/bootstrap-select/css/bootstrap-select.css') }}}" rel="stylesheet" /> -->

    <!-- Morris Chart Css-->
    <link href="{{{ URL::asset('adminbsb/plugins/morrisjs/morris.css')}}}" rel="stylesheet" />

    <!-- JQuery DataTable Css -->
    <link href="{{{ URL::asset('adminbsb/plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css')}}}" rel="stylesheet">

    <!-- Dropzone Css -->
    <link href="{{{ URL::asset('adminbsb/plugins/dropzone/dropzone.css') }}}" rel="stylesheet">

    <!-- AdminBSB Themes. You can choose a theme from css/themes instead of get all themes -->
    <link href="{{{ URL::asset('adminbsb/css/themes/all-themes.css')}}}" rel="stylesheet" />

    <!-- Sweet Alert Css -->
    <link href="{{{ URL::asset('adminbsb/plugins/sweetalert/sweetalert.css') }}}" rel="stylesheet" />
    
    <!-- Colorpicker Css -->
    <link href="{{{ URL::asset('adminbsb/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css') }}}" rel="stylesheet" />

    <!-- Material icons; application typography uses a fast system font stack. -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    <!-- Light Gallery Plugin Css -->
    <link href="{{{ URL::asset('adminbsb/plugins/light-gallery/css/lightgallery.css') }}}" rel="stylesheet">
    
    <!-- Custom Css -->
    <link href="{{{ URL::asset('adminbsb/css/style.css')}}}" rel="stylesheet">
    
    <link rel="stylesheet" type="text/css" href="{{ asset('Assets/custom-loading.css') }}">
    <!-- PondokHuda design layer: keep legacy AdminBSB intact, override safely. -->
    <link href="{{ asset('css/pondokhuda-modern.css') }}" rel="stylesheet">
    @yield('css-content')
</head>
