@extends('adminlte::page')
@section('title', 'Settings')
@section('plugins.Datatables', true)
@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/daterangepicker/daterangepicker.css') }}" >
    <link rel="stylesheet" href="{{ asset('admin/assets/css/adminStyles.css') }}" >
@endsection
@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Settings</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('core.admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="#">Settings</a></li>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@stop
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="col-12 table-responsive">
                @php
                    $heads = [
                        'Group',
                        'Title',
                        'Value',
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
                    'ajax' => route('core.admin.ajax.setting.index'),
                    'order' => [[4, 'desc']],
                    'columns' => [
                        ['data' => 'group'],
                        ['data' => 'title'],
                        ['data' => 'value', 'orderable' => false],
                        ['data' => 'created_at', 'visible' => false],
                        ['data' => 'date_formatted', 'orderData' => 3, 'target' => 3],
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
                                                <label for="group">Group</label>
                                                <input type="text" data-column="0" name="group" class="form-control" id="group" placeholder="Enter group">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="title">Title</label>
                                                <input type="text" data-column="1" name="title" class="form-control" id="title" placeholder="Enter title">
                                            </div>
                                        </div>
                                    </div>
                                    @include('default_view::admin.parts.date-range-filter', ['column' => 3])
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
@endsection
@section('js')
    <script src="{{ asset('vendor/datatables-plugins/responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="//cdn.datatables.net/datetime/1.1.1/js/dataTables.dateTime.min.js"></script>
    <script src="{{ asset('vendor/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('admin/assets/js/adminDatatables.js') }}"></script>
@endsection
