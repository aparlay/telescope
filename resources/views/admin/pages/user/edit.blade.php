@extends('adminlte::page')
@section('title', 'User Profile')
@section('plugins.Datatables', true)
@section('plugins.Select2', true)
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/assets/css/uploadMedia.css') }}" >
@stop
@section('content_header')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">User Profile
                        <button class="ml-4 btn btn-sm btn-danger col-md-1" data-toggle="modal" data-target="#alertModal">
                            <i class="fas fa-minus-circle"></i>
                            Alert
                        </button>
                    </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('core.admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('core.admin.user.index') }}">Users</a></li>
                        <li class="breadcrumb-item">Details</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
@stop
@section('content')
    @include('default_view::admin.parts.messages')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">
                    <div class="card card-primary card-outline">
                        <div class="ribbon-wrapper ribbon-xl">
                            <div class="ribbon bg-{{ $user->status_badge['color'] }}">
                                {{ $user->status_badge['status'] }}
                            </div>
                        </div>
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <img src="{{ $user->avatar }}?aspect_ratio=1:1&width=150" alt="" class="profile-user-img img-fluid img-circle">
                            </div>
                            <h3 class="profile-username text-center">{{ $user->username }}</h3>
                            <p class="text-muted text-center">
                                <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                            </p>
                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Medias</b>
                                    <a class="float-right">{{ $user->media_count }}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Followers</b>
                                    <a class="float-right">{{ $user->follower_count }}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Following</b>
                                    <a class="float-right">{{ $user->following_count }}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Blocks</b>
                                    <a class="float-right">{{ $user->block_count }}</a>
                                </li>
                            </ul>
                            <div class="row">
                                <div class="col-md-6">
                                    @if($user->status == \Aparlay\Core\Models\Enums\UserStatus::SUSPENDED->value)
                                        <button type="button" class="btn btn-block btn-success" data-toggle="modal" data-target="#activateModal">
                                            <i class="fas fa-check"></i>
                                            <strong>Reactivate</strong>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-block btn-warning" data-toggle="modal" data-target="#suspendModal">
                                            <i class="fas fa-minus-circle"></i>
                                            <strong>Suspend</strong>
                                        </button>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    @if($user->status == \Aparlay\Core\Models\Enums\UserStatus::BLOCKED->value)
                                        <button type="button" class="btn btn-block btn-success" data-toggle="modal" data-target="#activateModal">
                                            <i class="fas fa-check"></i>
                                            <strong>Reactivate</strong>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-block btn-danger" data-toggle="modal" data-target="#banModal">
                                            <i class="fas fa-times-circle"></i>
                                            <strong>Ban</strong>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-items">
                                    <a href="#user-info" class="nav-link active" data-toggle="tab">User Information</a>
                                </li>
                                <li class="nav-items">
                                    <a href="#medias" class="nav-link" data-toggle="tab">Medias</a>
                                </li>
                                <li class="nav-items">
                                    <a href="#upload" class="nav-link" data-toggle="tab">Upload</a>
                                </li>
                                <li class="nav-items">
                                    <a href="#payment" class="nav-link" data-toggle="tab">Payments</a>
                                </li>
                                <li class="nav-items">
                                    <a href="#documents" class="nav-link" data-toggle="tab">Documents</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">

                                @include('default_view::admin.pages.user.tabs.user-info', ['user' => $user])
                                @include('default_view::admin.pages.user.tabs.medias', ['user' => $user])
                                @include('default_view::admin.pages.user.tabs.upload', ['user' => $user])
                                @include('default_view::admin.pages.user.tabs.payment', ['user' => $user])
                                @include('default_view::admin.pages.user.tabs.documents', ['user' => $user])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="alertModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <form action="{{ route('core.admin.alert.store') }}" method="post">
                <input type="hidden" name="user_id" value="{{ $user->_id }}">
                <input type="hidden" name="type" value="{{ \Aparlay\Core\Models\Enums\AlertType::USER->value}}">
                <input type="hidden" name="status" value="{{ \Aparlay\Core\Models\Enums\AlertStatus::NOT_VISITED->value }}">
                <!-- Modal content-->
                <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLiveLabel">Alert User</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Reason <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="reason" placeholder="Type your message." />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                </div>
            </form>
        </div>
    </div>
    <div id="banModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <form action="{{ route('core.admin.user.update.status', ['user' => $user->_id])  }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" value="{{ \Aparlay\Core\Models\Enums\UserStatus::BLOCKED->value }}" name="status">
                        <div class="modal-header bg-danger">
                            <h5 class="modal-title" id="exampleModalLiveLabel">Ban User</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to ban this user?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                            <button type="submit" class="btn btn-danger">Ban</button>
                        </div>
                    </form>
                </div>
        </div>
    </div>
    <div id="suspendModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <form action="{{ route('core.admin.user.update.status', ['user' => $user->_id])  }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="{{ \Aparlay\Core\Models\Enums\UserStatus::SUSPENDED->value }}">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title" id="exampleModalLiveLabel">Suspend</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to suspend this user?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                            <button type="submit" class="btn btn-warning">Suspend</button>

                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="activateModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <form action="{{ route('core.admin.user.update.status', ['user' => $user->_id])  }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="{{ \Aparlay\Core\Models\Enums\UserStatus::ACTIVE->value }}">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title" id="exampleModalLiveLabel">Reactivate</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to reactivate this user?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                        <button type="submit" class="btn btn-warning">Reactivate</button>

                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('vendor/datatables-plugins/responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('admin/assets/js/adminDatatables.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/flow/flow.min.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/uploadMedia.js') }}"></script>
@endsection


