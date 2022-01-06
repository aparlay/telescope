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
                <li class="breadcrumb-item"><a href="#">User Documents</a></li>
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
                                'Username',
                                '',
                                'Status',
                                'Type',
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
                            'ajax' => route('core.admin.ajax.user-document.index'),
                            'columns' => [
                                ['data' => 'username_avatar', 'orderable' => false],
                                ['data' => 'status', 'visible' => false, 'orderable' => false],
                                ['data' => 'status_badge', 'orderable' => false],
                                ['data' => 'type_label', 'orderable' => false],
                                ['data' => 'view_document', 'orderable' => false],
                                ['data' => 'view_user', 'orderable' => false],
                                ['data' => 'approve_action', 'orderable' => false],
                                ['data' => 'reject_action', 'orderable' => false]
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
                                                        <label for="status">Status</label>
                                                        <select name="status" data-column="1" id="status" class="form-control">
                                                            <option value="">-Select-</option>
                                                            @foreach($documentStatuses as $key => $status)
                                                                <option value="{{ $key }}">{{ $status }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

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


        <div id="approveModal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <form action="{{ route('core.admin.user.document.edit', ['documentId' => 0])  }}" method="POST" name="approveForm">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" value="{{ \Aparlay\Core\Models\Enums\UserDocumentStatus::APPROVED->value }}" name="status">
                        <div class="modal-header bg-success">
                            <h5 class="modal-title" id="exampleModalLiveLabel">Approve Document</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Mark this user document as approved?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                            <button type="submit" class="btn btn-success">Approve</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="rejectModal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <form action="{{ route('core.admin.user.document.edit', ['documentId' => 0])  }}" method="POST" name="rejectForm">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" value="{{ \Aparlay\Core\Models\Enums\UserDocumentStatus::REJECTED->value }}" name="status">
                        <div class="modal-header bg-danger">
                            <h5 class="modal-title" id="exampleModalLiveLabel">Reject Document</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Mark this user document as rejected?</p>

                            <div class="form-group">
                                <label for="recipient-name" class="col-form-label">Reject Reason:</label>
                                <input type="text" class="form-control" value="" name="reject_reason">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-danger">Reject</button>
                        </div>
                    </form>
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


    <script>
        $(function () {
         $('#approveModal').on("show.bs.modal", function (e) {
            document.approveForm.action = "{{ route('core.admin.user.document.edit', ['documentId' => '/']) }}" + '/' + $(e.relatedTarget).data('id');
            return e;
         });
        });
    </script>
@endsection

