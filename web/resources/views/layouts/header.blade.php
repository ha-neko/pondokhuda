<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    @auth
        @if(Auth::guard('super-owner')->check())
        <title> Si Juragan Kost | {{ $data['pageTitle'] }} </title>
        @else
        <title> {{ $appName }} | {{ $data['pageTitle'] }} </title>
        @endif
    @else
    <title> Si Juragan Kost | {{ $data['pageTitle'] }} </title>
    @endauth
    <!-- Favicon-->
    <link rel="icon" href="favicon.ico" type="image/x-icon">

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

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300" rel="stylesheet"> 

    <!-- Light Gallery Plugin Css -->
    <link href="{{{ URL::asset('adminbsb/plugins/light-gallery/css/lightgallery.css') }}}" rel="stylesheet">
    
    <!-- Custom Css -->
    <link href="{{{ URL::asset('adminbsb/css/style.css')}}}" rel="stylesheet">
    
    <link rel="stylesheet" type="text/css" href="https://pondok-huda.com/Assets/custom-loading.css">
    @yield('css-content')
</head>