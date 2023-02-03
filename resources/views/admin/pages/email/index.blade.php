@extends('adminlte::page')
@section('title', 'Emails')

@section('css')
    <link rel="stylesheet" href="/css/admin.css" >
    @livewireStyles
@stop

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0"></h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('core.admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="#">Emails</a></li>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@stop


@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 table-responsive">
                    <livewire:emails-table />
                </div>
            </div>
        </div>
    </div>
@endsection


@section('js')
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>
    <livewire:modals/>
    <script src="/js/admin.js"></script>
@endsection
