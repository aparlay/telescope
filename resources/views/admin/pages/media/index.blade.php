@extends('adminlte::page')
@section('plugins.Datatables', true)
@section('title', 'Media')
@section('css')
    <link rel="stylesheet" href="{{ asset('admin/assets/css/media.css') }}">
@endsection
@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Users</h1>
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
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    @include('default_view::admin.parts.breadcrumbs')

    <table id="datatables" class="table table-bordered table-hover" width="100%">
        <thead>
        <tr>
            <th scope="col">Cover</th>
            <th scope="col">Created By</th>
            <th scope="col">Description</th>
            <th scope="col">Status</th>
            <th scope="col">Created At</th>
            <th scope="col">Likes</th>
            <th scope="col">Visits</th>
            <th scope="col">Sort Score</th>
            <th scope="col"></th>
        </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
@endsection
@section('js')
    <script src="{{ asset('vendor/datatables-plugins/responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script>
        var mediaStatus = '<option value=""></option>';
        @foreach($mediaStatuses as $key => $status)
            mediaStatus += '<option value="' + {{ $key }} + '">{{ $status }}</option>'
        @endforeach
    </script>
    <script src="{{ asset('admin/assets/js/adminDatatables.js') }}"></script>
@endsection
