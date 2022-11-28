@php
    use Aparlay\Core\Models\Enums\UserStatus;
    use Aparlay\Core\Models\Enums\UserVisibility;
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
                        <div class="card-header">
                            <h3 class="card-title text-uppercase">Info</h3>
                        </div>
                        <div class="card-body">
                            @include('default_view::admin.pages.user.tabs.edit.profile', ['user' => $user])
                            @include('default_view::admin.pages.user.tabs.edit.user-info', ['user' => $user])
                            @include('default_view::admin.pages.user.tabs.edit.general', ['user' => $user])
                            @include('default_view::admin.pages.user.tabs.edit.payouts', ['user' => $user])
                        </div>
                    </div>

                    <div class="card card-default collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title text-uppercase">Chats</h3>
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
                            <livewire:chats-table :userId="$user->_id"/>
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
                                ><i class="fas fa-chevron-up"></i></button>
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
                                ><i class="fas fa-chevron-up"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                        </div>
                    </div>

                    <div class="card card-default collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title text-uppercase">Payouts</h3>
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
                            @include('default_view::admin.pages.user.tabs.payouts', ['user' => $user])
                        </div>
                    </div>

                    <div class="card card-default collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title text-uppercase">Cards</h3>
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
                            @include('default_view::admin.pages.user.tabs.cards', ['user' => $user])
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
                                ><i class="fas fa-chevron-up"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            @include('default_view::admin.pages.user.tabs.payment', ['user' => $user])
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
                                ><i class="fas fa-chevron-up"></i></button>
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
                                ><i class="fas fa-chevron-up"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                        </div>
                    </div>

                    <div class="card card-default collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title text-uppercase">Media</h3>
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
                            @include('default_view::admin.pages.user.tabs.upload', ['user' => $user])
                            @include('default_view::admin.pages.user.tabs.medias', ['user' => $user])
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
                                ><i class="fas fa-chevron-up"></i></button>
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
                    <input type="hidden" value="{{ UserStatus::BLOCKED->value }}"
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
                           value="{{ UserStatus::SUSPENDED->value }}">
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
                           value="{{ UserStatus::ACTIVE->value }}">
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
                    value="{{ UserVisibility::PRIVATE->value }}">
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
                           value="{{ UserVisibility::PUBLIC->value }}">
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
@endsection

@section('js')
    <script src="{{ URL::asset('admin/assets/js/ekko-lightbox.min.js') }}"></script>

    <script src="{{ URL::asset('admin/assets/js/flow/flow.min.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/uploadMedia.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>
    @livewireScripts
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
                console.log(card);
                $('#' + card + ' button.card-edit').addClass('d-none');
                $('#' + card + ' .data-show').addClass('d-none');

                $('#' + card + ' button.card-save').removeClass('d-none');
                $('#' + card + ' .data-edit').removeClass('d-none');
            });
        });
    </script>
@endsection
