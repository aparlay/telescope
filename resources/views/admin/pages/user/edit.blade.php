@php
    use Aparlay\Chat\Models\Chat;
    use Aparlay\Core\Models\MediaComment;
    use Aparlay\Core\Models\Enums\UserVerificationStatus;
    use Aparlay\Core\Models\Media;
@endphp
@extends('adminlte::page')
@section('title', 'User Profile')
@section('plugins.Datatables', true)
@section('plugins.Select2', true)
@section('css')
    <link rel="stylesheet" href="/css/admin.css">
    <link rel="stylesheet" href="/admin/assets/css/uploadMedia.css">
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
            <div class="row mt-0">
                <div class="col-12">
                    <div class="card card-default p-2 ml-n2">
                        @include('default_view::admin.pages.user.tabs.notes', ['user' => $user])
                    </div>
                </div>
            </div>
            <div class="row mt-0">
                <div class="col-md-3">
                    @include('default_view::admin.pages.user.tabs.edit.menu')
                </div>
                <div class="col-md-9 pl-2">
                    <div class="">
                        @include('default_view::admin.pages.user.tabs.edit.statistics')
                    </div>

                    <div class="card card-default">
                        <div class="card-body">
                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#tab-info">Info</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab-media">Media <span class="badge badge-primary">{{ Media::query()->creator((string) $user->_id)->count() }}</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab-comments">Comments <span class="badge badge-primary">{{ MediaComment::query()->creator((string) $user->_id)->count() }}</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab-chats">Chats <span class="badge badge-primary">{{ Chat::query()->activeFor((string) $user->_id)->participants((string) $user->_id)->count() }}</span></a>
                                </li>
                                <li class="nav-item d-none">
                                    <a class="nav-link" data-toggle="tab" href="#tab-payments">Payouts</a>
                                </li>
                                <li class="nav-item d-none">
                                    <a class="nav-link" data-toggle="tab" href="#tab-cards">Cards</a>
                                </li>
                                @if(in_array($user->verification_status, [UserVerificationStatus::PENDING->value, UserVerificationStatus::UNDER_REVIEW->value]))
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#tab-verification">Verification</a>
                                    </li>
                                @endif
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane container active" id="tab-info">
                                    @include('default_view::admin.pages.user.tabs.edit.profile', ['user' => $user])
                                    @include('default_view::admin.pages.user.tabs.edit.user-info', ['user' => $user])
                                    @include('default_view::admin.pages.user.tabs.edit.general', ['user' => $user])
                                    @include('default_view::admin.pages.user.tabs.edit.payouts', ['user' => $user])
                                </div>
                                <div class="tab-pane container fade" id="tab-media">
                                    @include('default_view::admin.pages.user.tabs.medias', ['user' => $user])
                                </div>
                                <div class="tab-pane container fade" id="tab-comments">
                                    <livewire:media-comments-table :userId="$user->_id"/>
                                </div>
                                <div class="tab-pane container fade" id="tab-chats">
                                    <livewire:chats-table :userId="$user->_id"/>
                                </div>
                                <div class="d-none tab-pane container fade" id="tab-payments">
                                    @include('default_view::admin.pages.user.tabs.payment', ['user' => $user])
                                </div>
                                <div class="d-none tab-pane container fade" id="tab-cards">
                                    @include('default_view::admin.pages.user.tabs.cards', ['user' => $user])
                                </div>
                                @if(in_array($user->verification_status, [UserVerificationStatus::PENDING->value, UserVerificationStatus::UNDER_REVIEW->value]))
                                    <div class="tab-pane container fade" id="tab-verification">
                                        <livewire:user-verification :userId="$user->_id"/>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card card-default collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title text-uppercase">Devices</h3>
                            <div class="card-tools">
                                <button
                                    type="button"
                                    class="btn btn-tool"
                                    data-card-widget="collapse"
                                    data-expand-icon="fa-chevron-down"
                                    data-collapse-icon="fa-chevron-up"
                                ><i class="fas fa-chevron-down"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            @include('default_view::admin.pages.user.tabs.device', ['user' => $user])
                        </div>
                    </div>

                    <div class="card card-default collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title text-uppercase">Sales</h3>
                            <div class="card-tools">
                                <button
                                    type="button"
                                    class="btn btn-tool"
                                    data-card-widget="collapse"
                                    data-expand-icon="fa-chevron-down"
                                    data-collapse-icon="fa-chevron-up"
                                ><i class="fas fa-chevron-down"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                        </div>
                    </div>

                    <div class="card card-default collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title text-uppercase">Payments</h3>
                            <div class="card-tools">
                                <button
                                    type="button"
                                    class="btn btn-tool"
                                    data-card-widget="collapse"
                                    data-expand-icon="fa-chevron-down"
                                    data-collapse-icon="fa-chevron-up"
                                ><i class="fas fa-chevron-down"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                        </div>
                    </div>

                    <div class="card card-default collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title text-uppercase">Emails</h3>
                            <div class="card-tools">
                                <button
                                    type="button"
                                    class="btn btn-tool"
                                    data-card-widget="collapse"
                                    data-expand-icon="fa-chevron-down"
                                    data-collapse-icon="fa-chevron-up"
                                ><i class="fas fa-chevron-down"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            @include('default_view::admin.pages.user.tabs.email', ['user' => $user])
                        </div>
                    </div>

                    <div class="card card-default collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title text-uppercase">Two-Factor Authentication</h3>
                            <div class="card-tools">
                                <button
                                    type="button"
                                    class="btn btn-tool"
                                    data-card-widget="collapse"
                                    data-expand-icon="fa-chevron-down"
                                    data-collapse-icon="fa-chevron-up"
                                ><i class="fas fa-chevron-down"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                        </div>
                    </div>

                    <div class="card card-default collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title text-uppercase">Wallets</h3>
                            <div class="card-tools">
                                <button
                                    type="button"
                                    class="btn btn-tool"
                                    data-card-widget="collapse"
                                    data-expand-icon="fa-chevron-down"
                                    data-collapse-icon="fa-chevron-up"
                                ><i class="fas fa-chevron-down"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            @include('default_view::admin.pages.user.tabs.wallets', ['user' => $user])
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

    <div id="invisibleModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <form action="{{ route('core.admin.user.update.visibility', ['user' => $user->_id])  }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="visibility"
                    value="{{ \Aparlay\Core\Models\Enums\UserVisibility::INVISIBLE_BY_ADMIN->value }}">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title" id="exampleModalLiveLabel">Make invisible</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to make creator invisible?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Confirm</button>
                  </div>
                </form>
            </div>
        </div>
    </div>

    <div id="publicModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <form action="{{ route('core.admin.user.update.visibility', ['user' => $user->_id])  }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="visibility"
                           value="{{ \Aparlay\Core\Models\Enums\UserVisibility::PUBLIC->value }}">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title" id="exampleModalLiveLabel">Make public</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to make creator public?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Confirm</button>
                  </div>
                </form>
            </div>
        </div>
    </div>

    @include('default_view::admin.pages.user.modals.ban_payout', ['user' => $user, 'method' => 'set'])
    @include('default_view::admin.pages.user.modals.ban_payout', ['user' => $user, 'method' => 'unset'])

    @include('default_view::admin.pages.user.modals.auto_ban_payout', ['user' => $user, 'method' => 'set'])
    @include('default_view::admin.pages.user.modals.auto_ban_payout', ['user' => $user, 'method' => 'unset'])

    <div id="changeUsernameModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="changeUserNameModalLabel">Please confirm to change username</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to change the username? Videos will be reprocessed.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" data-dismiss="modal" id="confirmChangeUsername">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <div id="changePasswordModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <form action="{{ route('core.admin.user.update.password', ['user' => $user->_id])  }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title" id="changePasswordModalLabel">Please set a new password</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Set a new password</p>
                        <div class="form-group row m-0">
                            <label for="password" class="col-sm-4 col-form-label">Password</label>
                            <div class="col-sm-8 mt-2 pl-4">
                                <input type="password" class="form-control data-edit" id="password" name="password">
                            </div>
                        </div>
                        <div class="form-group row m-0">
                            <label for="password_confirmation" class="col-sm-4 col-form-label">Password again</label>
                            <div class="col-sm-8 mt-2 pl-4">
                                <input type="password" class="form-control data-edit" id="password_confirmation" name="password_confirmation">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script src="{{ URL::asset('admin/assets/js/ekko-lightbox.min.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/flow/flow.min.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/uploadMedia.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>
    <livewire:modals/>
    <script src="/js/admin.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            let $originalUsername = $('#profile-form #username').val();

            $('#profile-form').on('submit', function(e) {
                e.preventDefault();

                if ($(e.target).find('#username').val() === $originalUsername) {
                    e.target.submit();
                } else {
                    $('#changeUsernameModal').modal();
                    $('#confirmChangeUsername').on('click', function() {
                        e.target.submit();
                    });
                }
            });

            $('.user-profile-card button.card-edit').on('click', function() {
                let card = $(this).data('edit');

                $('#' + card + ' button.card-edit').addClass('d-none');
                $('#' + card + ' .data-show').addClass('d-none');

                $('#' + card + ' button.card-save').removeClass('d-none');
                $('#' + card + ' .data-edit').removeClass('d-none');
            });
        });
    </script>
@endpush
