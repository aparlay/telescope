@extends('adminlte::page')
@section('plugins.Datatables', true)
@section('title', 'Email')
@section('css')
    <link rel="stylesheet" href="{{ asset('admin/assets/css/email.css') }}">
@endsection
@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Email</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('core.admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="#">Email</a></li>
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
                    @php
                        $heads = [
                            '',
                            'User',
                            'Email',
                            '',
                            'Type',
                            '',
                            'Status',
                            'Created At',
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
                        'ajax' => route('core.admin.ajax.email.index'),
                        'order' => [[0, 'desc']],
                        'columns' => [
                            ['data' => 'attributes.user.username', 'visible' => false],
                            ['data' => 'user_info', 'orderData' => 0, 'target' => 0],
                            ['data' => 'attributes.to'],
                            ['data' => 'attributes.type', 'visible' => false],
                            ['data' => 'type_text', 'orderData' => 3, 'target' => 3],
                            ['data' => 'attributes.status', 'visible' => false],
                            ['data' => 'status_text', 'orderData' => 5, 'target' => 5],
                            ['data' => 'created_at'],
                        ],
                    ];
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
                                                    <label for="attributes.user.username">User</label>
                                                    <input type="text" data-column="0" name="attributes.user.username" class="form-control" id="attributes.user.username" placeholder="Enter username">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="attributes.to">Email</label>
                                                    <input type="text" data-column="2" name="attributes.to" class="form-control" id="attributes.to" placeholder="Enter email">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">

                                                <div class="form-group">
                                                    <label for="status">Type</label>
                                                    <select name="status" data-column="3" id="status" class="form-control">
                                                        <option value="">-Select-</option>
                                                        @foreach($emailTypes as $key => $type)
                                                            <option value="{{ $key+1 }}">{{ $type }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                            </div>
                                            <div class="col-sm-3">

                                                    <div class="form-group">
                                                        <label for="status">Status</label>
                                                        <select name="status" data-column="5" id="status" class="form-control">
                                                            <option value="">-Select-</option>
                                                            @foreach($emailStatuses as $key => $status)
                                                                <option value="{{ $key }}">{{ $status }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                            </div>
                                            <div class="col-sm-12">
                                                <div class="row d-flex justify-content-end">
                                                    <div class="col-6"></div>
                                                    <div class="col-3">
                                                        <label for="clearFilter"></label>
                                                        <button type="button" id="clearFilter" class="btn btn-block btn-danger"><i class="fas fa-trash"></i> Clear</button>
                                                    </div>
                                                    <div class="col-3">
                                                        <label for="submitFilter"></label>
                                                        <button type="button" id="submitFilter" class="btn btn-block btn-primary"><i class="fas fa-filter"></i> Filter</button>
                                                    </div>
                                                </div>
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
    <script src="{{ asset('admin/assets/js/adminDatatables.js') }}"></script>
@endsection
