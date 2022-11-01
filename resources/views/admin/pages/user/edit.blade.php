@extends('adminlte::page')
@section('title', 'User Profile')
@section('plugins.Datatables', true)
@section('plugins.Select2', true)
@section('css')
    <link rel="stylesheet" href="/css/admin.css">
    @livewireStyles
@stop
@section('content_header')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('core.admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('core.admin.user.index') }}">Users</a></li>
                        <li class="breadcrumb-item">{{ $user->username }}</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@stop
@section('content')
    @include('default_view::admin.parts.messages')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-2">
                    <div class="row text-center">
                        <img src="{{ $user->avatar }}?aspect_ratio=1:1&width=150" alt=""
                            class="profile-user-img img-fluid img-circle">
                    </div>
                </div>
                <div class="col-md-10">
                    @include('default_view::admin.pages.user.tabs.statisticsnew')

                    <div class="card card-default">
                        <div class="card-header">
                            <h3 class="card-title">Notes</h3>
                            <div class="card-tools">
                                <button
                                    type="button"
                                    class="btn btn-tool"
                                    data-card-widget="collapse"
                                    data-expand-icon="fa-chevron-down"
                                    data-collapse-icon="fa-chevron-up"
                                ><i class="fas fa-chevron-up"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            @include('default_view::admin.pages.user.tabs.notes', ['user' => $user])
                        </div>
                    </div>

                    <div class="card card-default">
                        <div class="card-header">
                            <h3 class="card-title">Info</h3>
                        </div>
                        <div class="card-body">
                            @include('default_view::admin.pages.user.tabs.edit.profile', ['user' => $user])
                            @include('default_view::admin.pages.user.tabs.edit.user-info', ['user' => $user])
                            @include('default_view::admin.pages.user.tabs.edit.general', ['user' => $user])
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-3">
                    <div class="card card-primary card-outline row">
                        <div class="ribbon-wrapper ribbon-xl">
                            <div class="ribbon bg-{{ $user->status_badge['color'] }}">
                                {{ $user->status_badge['status'] }}
                            </div>
                        </div>
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <img src="{{ $user->avatar }}?aspect_ratio=1:1&width=150" alt=""
                                     class="profile-user-img img-fluid img-circle">
                            </div>
                            <h3 class="profile-username text-center">
                                {{ $user->username }}
                                <i title="{{$user->is_online ? 'online' : 'offline'}}" @class(['fa-user', 'ml-1', 'fas text-success' => $user->is_online, 'far text-gray' => !$user->is_online])></i>
                                @if ($user->is_verified)
                                    <img src="{{ asset('admin/assets/img/verify-16.png') }}" alt="Verified">
                                @endif
                            </h3>
                            <p class="text-muted text-center">
                                <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                            </p>
                            <hr>
                            <h5>Summary:</h5>
                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Medias</b>
                                    <a class="float-right">{{ $user->stats['counters']['medias'] }}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Followers</b>
                                    <a class="float-right">{{ $user->stats['counters']['followers'] }}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Following</b>
                                    <a class="float-right">{{ $user->stats['counters']['followings'] }}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Blocks</b>
                                    <a class="float-right">{{ $user->stats['counters']['blocks'] }}</a>
                                </li>
                            </ul>
                            <div class="row">
                                <div class="col-md-6">
                                    @if($user->status == \Aparlay\Core\Models\Enums\UserStatus::SUSPENDED->value)
                                        <button type="button" class="btn btn-block btn-success"
                                                data-toggle="modal" data-target="#activateModal">
                                            <i class="fas fa-check"></i>
                                            <strong>Reactivate</strong>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-block btn-warning"
                                                data-toggle="modal" data-target="#suspendModal">
                                            <i class="fas fa-minus-circle"></i>
                                            <strong>Suspend</strong>
                                        </button>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    @if($user->status == \Aparlay\Core\Models\Enums\UserStatus::BLOCKED->value)
                                        <button type="button" class="btn btn-block btn-success"
                                                data-toggle="modal" data-target="#activateModal">
                                            <i class="fas fa-check"></i>
                                            <strong>Reactivate</strong>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-block btn-danger"
                                                data-toggle="modal" data-target="#banModal">
                                            <i class="fas fa-times-circle"></i>
                                            <strong>Ban</strong>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <a href="{{ route('core.admin.user.login_as_user', ['user' => $user->_id]) }}"
                                       class="btn btn-bock btn-dark d-block" target="_blank">
                                        <i class="fas fa-lock"></i>
                                        <strong>Login as User</strong>
                                    </a>
                                </div>
                            </div>
                            <hr>

                            <div class="row">
                                @if ($hasPrev)
                                    <div class="col-md-6">
                                        <a href="{{ route('core.admin.user.moderation-queue.next', ['userId' => $user->_id, 'direction' => -1]) }}"
                                           class="btn btn-info d-block">
                                            <strong><i class="fa fa-arrow-left"></i> Previous </strong>
                                        </a>
                                    </div>
                                @endif

                                @if($hasNext)
                                    <div class="col-md-6">
                                        <a href="{{ route('core.admin.user.moderation-queue.next', ['userId' => $user->_id, 'direction' => 1]) }}"
                                           class="btn btn-info d-block">
                                            <strong>Next <i class="fa fa-arrow-right"></i> </strong>
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <livewire:user-moderation-button :userId="$user->_id"/>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <button class="btn btn-block btn-warning" data-toggle="modal"
                                            data-target="#alertModal">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Send warning message
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <livewire:direct-chat :userId="$user->_id"
                                          :username="$user->username"
                                          :adminUserId="auth()->user()->_id"
                                          :adminUsername="auth()->user()->username"
                                          :category="\Aparlay\Chat\Models\Enums\ChatCategory::SUPPORT->value"
                                          :headerText="'Support Chat'"/>
                </div>
                <div class="col-md-9">
                    @include('default_view::admin.pages.user.tabs.statistics')
                    <div class="row">
                        <div id="accordion" class="col-md-12">
                            <div class="card card-danger card-outline">
                                <div class="card-header" id="headingMainTwo">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link collapsed" data-toggle="collapse"
                                                data-target="#collapseMainTwo" aria-expanded="false"
                                                aria-controls="collapseMainTwo">
                                            User Information
                                        </button>
                                    </h5>
                                </div>
                                <div id="collapseMainTwo" class="collapse show" aria-labelledby="headingMainTwo"
                                     data-parent="#accordion">
                                    <div class="card-body">
                                        @include('default_view::admin.pages.user.tabs.user-info', ['user' => $user])
                                    </div>
                                </div>
                            </div>
                            <div class="card card-warning card-outline">
                                <div class="card-header" id="headingMainThree">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link collapsed" data-toggle="collapse"
                                                data-target="#collapseMainThree" aria-expanded="false"
                                                aria-controls="collapseMainThree">
                                            Medias
                                        </button>
                                    </h5>
                                </div>
                                <div id="collapseMainThree" class="collapse" aria-labelledby="headingMainThree"
                                     data-parent="#accordion">
                                    <div class="card-body">
                                        @include('default_view::admin.pages.user.tabs.upload', ['user' => $user])
                                        @include('default_view::admin.pages.user.tabs.medias', ['user' => $user])
                                    </div>
                                </div>
                            </div>
                            <div class="card card-success card-outline">
                                <div class="card-header" id="headingMainSix">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link collapsed" data-toggle="collapse"
                                                data-target="#collapseMainSix" aria-expanded="false"
                                                aria-controls="collapseMainSix">
                                            Payments
                                        </button>
                                    </h5>
                                </div>
                                <div id="collapseMainSix" class="collapse" aria-labelledby="headingMainSix"
                                     data-parent="#accordion">
                                    <div class="card-body">
                                        @include('default_view::admin.pages.user.tabs.payment', ['user' => $user])
                                    </div>
                                </div>
                            </div>
                            <div class="card card-indigo card-outline">
                                <div class="card-header" id="headingMainSeven">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link collapsed" data-toggle="collapse"
                                                data-target="#collapseMainSeven" aria-expanded="false"
                                                aria-controls="collapseMainSeven">
                                            Devices
                                        </button>
                                    </h5>
                                </div>
                                <div id="collapseMainSeven" class="collapse" aria-labelledby="headingMainSeven"
                                     data-parent="#accordion">
                                    <div class="card-body">
                                        @include('default_view::admin.pages.user.tabs.device', ['user' => $user])
                                    </div>
                                </div>
                            </div>
                            <div class="card card-lime card-outline">
                                <div class="card-header" id="headingMainEight">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link collapsed" data-toggle="collapse"
                                                data-target="#collapseMainEight" aria-expanded="false"
                                                aria-controls="collapseMainEight">
                                            Email
                                        </button>
                                    </h5>
                                </div>
                                <div id="collapseMainEight" class="collapse" aria-labelledby="headingMainEight"
                                     data-parent="#accordion">
                                    <div class="card-body">
                                        @include('default_view::admin.pages.user.tabs.email', ['user' => $user])
                                    </div>
                                </div>
                            </div>
                            <div class="card card-primary card-outline">
                                <div class="card-header" id="headingNotes">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link collapsed" data-toggle="collapse"
                                                data-target="#collapseNotes" aria-expanded="false"
                                                aria-controls="collapseNotes">
                                            Notes
                                        </button>
                                    </h5>
                                </div>
                                <div id="collapseNotes" class="collapse" aria-labelledby="collapseNotes"
                                     data-parent="#accordion">
                                    <div class="card-body">
                                        @include('default_view::admin.pages.user.tabs.notes', ['user' => $user])
                                    </div>
                                </div>
                            </div>
                            <div class="card card-primary card-outline">
                                <div class="card-header" id="headingMainNine">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link collapsed" data-toggle="collapse"
                                                data-target="#collapseMainNine" aria-expanded="false"
                                                aria-controls="collapseMainNine">
                                            Wallets
                                        </button>
                                    </h5>
                                </div>
                                <div id="collapseMainNine" class="collapse" aria-labelledby="headingMainNine"
                                     data-parent="#accordion">
                                    <div class="card-body">
                                        @include('default_view::admin.pages.user.tabs.wallets', ['user' => $user])
                                    </div>
                                </div>
                            </div>
                            <div class="card card-maroon card-outline">
                                <div class="card-header" id="headingMainTen">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link collapsed" data-toggle="collapse"
                                                data-target="#collapseMainTen" aria-expanded="false"
                                                aria-controls="collapseMainTen">
                                            Chats
                                        </button>
                                    </h5>
                                </div>
                                <div id="collapseMainTen" class="collapse" aria-labelledby="headingMainTen"
                                     data-parent="#accordion">
                                    <div class="card-body">
                                        @include('default_view::admin.pages.user.tabs.chats', ['user' => $user])
                                    </div>
                                </div>
                            </div>

                            <div class="card card-maroon card-outline">
                                <div class="card-header" id="headingMainTen">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link collapsed" data-toggle="collapse"
                                                data-target="#payoutsTab" aria-expanded="false"
                                                aria-controls="collapseMainTen">
                                            Payouts
                                        </button>
                                    </h5>
                                </div>
                                <div id="payoutsTab" class="collapse" aria-labelledby="headingMainTen"
                                     data-parent="#accordion">
                                    <div class="card-body">
                                        @include('default_view::admin.pages.user.tabs.payouts', ['user' => $user])
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="alertModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <form action="{{ route('core.admin.alert.store.user', ['user' => $user->_id ]) }}" method="post">
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
                            <input type="text" class="form-control" name="reason"
                                   placeholder="Type your message."/>
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
                <form action="{{ route('core.admin.user.update.status', ['user' => $user->_id])  }}"
                      method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" value="{{ \Aparlay\Core\Models\Enums\UserStatus::BLOCKED->value }}"
                           name="status">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title" id="exampleModalLiveLabel">Block User</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to block this user?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                        <button type="submit" class="btn btn-danger">Block</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="suspendModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <form action="{{ route('core.admin.user.update.status', ['user' => $user->_id])  }}"
                      method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status"
                           value="{{ \Aparlay\Core\Models\Enums\UserStatus::SUSPENDED->value }}">
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
                <form action="{{ route('core.admin.user.update.status', ['user' => $user->_id])  }}"
                      method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status"
                           value="{{ \Aparlay\Core\Models\Enums\UserStatus::ACTIVE->value }}">
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
    <script src="{{ URL::asset('admin/assets/js/ekko-lightbox.min.js') }}"></script>

    <script src="{{ URL::asset('admin/assets/js/flow/flow.min.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/uploadMedia.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>
    @livewireScripts
    <livewire:modals/>
    <script src="/js/admin.js"></script>
@endsection
