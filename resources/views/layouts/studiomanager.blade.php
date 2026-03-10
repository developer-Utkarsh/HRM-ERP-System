<!DOCTYPE html>
<html class="loading" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="">
    <meta name="keywords" content="">   
    <title>{{ config('app.name') }} - Studio Manager Panel</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/vendors.min.css') }}">
    {{-- <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/apexcharts.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/tether-theme-arrows.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/tether.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/shepherd-theme-default.css') }}"> --}}
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/bootstrap-extended.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/colors.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/components.css') }}">
    {{-- <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/dark-layout.css') }}"> --}}
    {{-- <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/semi-dark-layout.css') }}"> --}}

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/vertical-menu.css') }}">
    {{-- <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/palette-gradient.css') }}"> --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/dashboard-analytics.css') }}">
    {{-- <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/card-analytics.css') }}"> --}}
    {{-- <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/tour.css') }}"> --}}
    <!-- END: Page CSS-->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <!-- BEGIN: Custom CSS-->
    {{-- <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/style.css') }}"> --}}
    <!-- END: Custom CSS-->
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap4.min.css">

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern 2-columns  navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">

    <!-- BEGIN: Header-->
    @include('layouts.studiomanager.header')
    <!-- END: Header-->
    <!-- BEGIN: Main Menu-->
    @include('layouts.studiomanager.sidebar')
    <!-- END: Main Menu-->

    <!-- BEGIN: Content-->
    @yield('content')
    <!-- END: Content-->

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    <!-- BEGIN: Footer-->
    @include('layouts.studiomanager.footer')
    <!-- END: Footer-->

    <!-- BEGIN: Vendor JS-->
    <script src="{{ asset('laravel/public/admin/js/vendors.min.js') }}"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
{{--<script src="{{ asset('laravel/public/admin/js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('laravel/public/admin/js/tether.min.js') }}"></script>
    <script src="{{ asset('laravel/public/admin/js/shepherd.min.js') }}"></script> --}}
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="{{ asset('laravel/public/admin/js/app-menu.js') }}"></script>
    <script src="{{ asset('laravel/public/admin/js/app.js') }}"></script>
    <script src="{{ asset('laravel/public/admin/js/components.js') }}"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    {{-- <script src="{{ asset('laravel/public/admin/js/dashboard-analytics.js') }}"></script> --}}
    <!-- END: Page JS-->

    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
	
	<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>

    {{-- <script src="//cdn.ckeditor.com/4.13.1/full/ckeditor.js"></script>
    <script>
        CKEDITOR.replace( 'ckeditor', {
            removeButtons: '',
            pasteFilter: false,
            specialChars: false,
        });
    </script> --}}
    @include('layouts.notification')
    @yield('scripts')
	@stack('js')
</body>
<!-- END: Body-->
</html>

