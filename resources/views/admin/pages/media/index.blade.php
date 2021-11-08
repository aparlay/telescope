@extends('adminlte::page')
@section('plugins.Datatables', true)
@section('title', 'Media')
@section('css')
    <link rel="stylesheet" href="{{ asset('admin/assets/css/media.css') }}">
@endsection
@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Media @if($moderation) moderation @endif</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('core.admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="#">Media @if($moderation) moderation @endif</a></li>
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
                            'Cover',
                            'Created By',
                            'Description',
                            '',
                            'Status',
                            'Likes',
                            'Visits',
                            'Sort Score',
                            'Created At',
                            '',
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
                        'ajax' => route('core.admin.ajax.media.index'),
                        'order' => [[8, 'desc']],
                        'columns' => [
                            ['data' => 'file', 'orderable' => false],
                            ['data' => 'creator.username'],
                            ['data' => 'description', 'orderable' => false],
                            ['data' => 'status', 'visible' => false],
                            ['data' => 'status_badge', 'orderData' => 3, 'target' => 3, 'orderable' => $moderation ? false : true],
                            ['data' => 'like_count', 'orderable' => false],
                            ['data' => 'visit_count', 'orderable' => false],
                            ['data' => 'sort_score', 'orderable' => false],
                            ['data' => 'created_at', 'visible' => false],
                            ['data' => 'date_formatted', 'orderData' => 8, 'target' => 8, 'orderable' => true],
                            ['data' => 'action', 'orderable' => false],
                        ],
                    ];
                    if($moderation){
                        $config['searching'] = true;
                        $config['searchCols'] = [null,null,null,["search" => 3]];
                        $config['bInfo'] = false;
                    }
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
                                                    <label for="username">Created By</label>
                                                    <input type="text" data-column="1" name="creator.username" class="form-control" id="creator.username" placeholder="Enter username">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                @if(!$moderation)
                                                    <div class="form-group">
                                                        <label for="status">Status</label>
                                                        <select name="status" data-column="3" id="status" class="form-control">
                                                            <option value="">-Select-</option>
                                                            @foreach($mediaStatuses as $key => $status)
                                                                <option value="{{ $key }}">{{ $status }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="row d-flex justify-content-end">
                                                    <div class="col-2">
                                                        <label for="clearFilter"></label>
                                                        <button type="button" id="clearFilter" class="btn btn-block btn-danger"><i class="fas fa-trash"></i> Clear</button>
                                                    </div>
                                                    <div class="col-2">
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
