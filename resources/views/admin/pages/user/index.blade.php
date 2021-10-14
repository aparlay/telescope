@extends('adminlte::page')
@section('title', 'Users')
@section('css')
    <link rel="stylesheet" href="{{ asset('admin/assets/css/user.css') }}">
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
                        <table id="datatables" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Fullname</th>
                                    <th>Status</th>
                                    <th>Visibility</th>
                                    <th>Followers</th>
                                    <th>Likes</th>
                                    <th>Medias</th>
                                    <th>Created_at</th>
                                    <th></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('vendor/datatables-plugins/responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script>
        var userStatus = '<option value=""></option>';

        @foreach($userStatuses as $key => $status)
            userStatus += '<option value="' + {{ $key }} + '">{{ $status }}</option>'
        @endforeach

        var userVisibility = '<option value=""></option>';

        @foreach($userVisibilities as $key => $visibility)
            userVisibility += '<option value="' + {{ $key }} + '">{{ $visibility }}</option>'
        @endforeach
    </script>
    <script src="{{ asset('admin/assets/js/adminDatatables.js') }}"></script>
@endsection

