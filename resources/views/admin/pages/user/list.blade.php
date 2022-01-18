@extends('adminlte::page')
@section('title', 'Users')
@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/daterangepicker/daterangepicker.css') }}" >
    <link rel="stylesheet" href="{{ asset('admin/assets/css/adminStyles.css') }}" >

    @livewireStyles
@stop
@section('plugins.Datatables', true)
@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Users</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('core.admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="#">Users</a></li>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@stop
@section('content')
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 table-responsive">
                        <livewire:users-table />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    @livewireScripts
@endsection

