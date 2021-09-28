<!doctype html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Alua | @yield('title')</title>
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&amp;display=fallback">
    <link rel="stylesheet" href="{{ asset('admin/assets/css/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/css/adminLTE/adminlte.min.css') }}">
    @yield('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
    @include('default_view::admin.parts.header')
    @include('default_view::admin.parts.left-sidebar')
    @yield('content')

</div>

<script src="{{ asset('admin/assets/js/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/adminLTE/adminlte.min.js') }}"></script>

@yield('scripts')
</body>
</html>
