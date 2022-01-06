@extends('adminlte::page')
@section('title', 'Users')
@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/daterangepicker/daterangepicker.css') }}" >
    <link rel="stylesheet" href="{{ asset('admin/assets/css/adminStyles.css') }}" >
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
                    @php
                        $heads = [
                            '',
                            'Username',
                            'Email',
                            'Fullname',
                            '',
                            'Status',
                            'Visibility',
                            'Followers',
                            'Likes',
                            'Medias',
                            '',
                            'Created at',
                            ''
                        ];

                    $config = [
                        'processing' => true,
                        'serverSide' => true,
                        'pageLength' => config('core.admin.lists.page_count'),
                        'responsive' => true,
                        'lengthChange' => false,
                        'dom' => 'rtip',
                        'orderMulti' => false,
                        'autoWidth' => false,
                        'ajax' => route('core.admin.ajax.user.index'),
                        'order' => [[11, 'desc']],
                        'columns' => [
                            ['data' => 'username', 'visible' => false],
                            ['data' => 'username_avatar', 'orderData' => 0, 'target' => 0],
                            ['data' => 'email'],
                            ['data' => 'full_name', 'orderable' => false],
                            ['data' => 'status', 'visible' => false],
                            ['data' => 'status_badge', 'orderData' => 4, 'target' => 4],
                            ['data' => 'visibility'],
                            ['data' => 'follower_count', 'orderable' => false],
                            ['data' => 'like_count', 'orderable' => false],
                            ['data' => 'media_count', 'orderable' => false],
                            ['data' => 'created_at', 'visible' => false],
                            ['data' => 'date_formatted', 'orderData' => 10, 'target' => 10],
                            ['data' => 'action', 'orderable' => false],
                        ],
                    ]
                    @endphp
                    <div id="accordion">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h4 class="card-title w-100">
                                    <a class="d-block w-100" data-toggle="collapse" href="#collapseOne" aria-expanded="true">
                                        Show/Hide Filter
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseOne" class="collapse" data-parent="#accordion" style="">
                                <div class="card-body">
                                    <form action="" id="filters">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="username">Username</label>
                                                    <input type="text" data-column="0" name="username" class="form-control" id="username" placeholder="Enter username">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="email">Email</label>
                                                    <input type="email" data-column="2" name="email" class="form-control" id="email" placeholder="Enter email">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="status">Status</label>
                                                    <select name="status" data-column="4" id="status" class="form-control">
                                                        <option value="">-Select-</option>
                                                        @foreach($userStatuses as $key => $status)
                                                            <option value="{{ $key }}">{{ $status }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="visibility">Visibility</label>
                                                    <select name="visibility" data-column="6" id="visibility" class="form-control">
                                                        <option value="">-Select-</option>
                                                        @foreach($userVisibilities as $key => $visibility)
                                                            <option value="{{ $key }}">{{ $visibility }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        @include('default_view::admin.parts.date-range-filter', ['column' => 10])
                                        <div class="row d-flex justify-content-end">
                                            <div class="col-1">
                                                <button type="button" id="clearFilter" class="btn btn-block btn-danger"><i class="fas fa-trash"></i> Clear</button>
                                            </div>
                                            <div class="col-1">
                                                <button type="button" id="submitFilter" class="btn btn-block btn-primary"><i class="fas fa-filter"></i> Filter</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <x-adminlte-datatable id="datatables" :heads="$heads" :config="$config">
                    </x-adminlte-datatable>
                </div>
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
@endsection

