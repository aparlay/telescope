@extends('adminlte::page')
@section('plugins.Datatables', true)
@section('title', 'Media')

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
                <li class="breadcrumb-item"><a href="#">Media</a></li>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@stop
@section('content')
    @include('default_view::admin.parts.messages')
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 table-responsive">
                <div class="card card-default">
                    <div class="card-body">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#medias-all">All</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#medias-moderation">Ready for Review</a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane container active" id="medias-all">
                                <livewire:medias-table/>
                            </div>
                            <div class="tab-pane container fade" id="medias-moderation">
                                <livewire:medias-moderation-table/>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>

@endsection


@section('js')
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>
    @livewireScripts
    <livewire:modals/>
    <script src="/js/admin.js"></script>
@endsection
