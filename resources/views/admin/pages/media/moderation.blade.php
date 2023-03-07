@extends('adminlte::page')
@section('plugins.Datatables', true)
@section('title', 'Media')
@section('css')
    <link rel="stylesheet" href="{{ asset('admin/assets/css/media.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/daterangepicker/daterangepicker.css') }}" >
    <link rel="stylesheet" href="{{ asset('admin/assets/css/adminStyles.css') }}" >
    @livewireStyles
@endsection
@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0"></h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('core.admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="#">Media moderation</a></li>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@stop
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 table-responsive">
                    <livewire:medias-moderation-table/>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('js')

    <script src="{{ asset('vendor/datatables-plugins/responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="//cdn.datatables.net/datetime/1.1.1/js/dataTables.dateTime.min.js"></script>
    <script src="{{ asset('vendor/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('admin/assets/js/adminDatatables.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>
@endsection
