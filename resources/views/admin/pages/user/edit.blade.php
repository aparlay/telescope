@extends('adminlte::page')
@section('title', 'User Profile')
@section('plugins.Datatables', true)
@section('plugins.Select2', true)
@section('content_header')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">User Profile
                        <button class="ml-4 btn btn-sm btn-danger col-md-1">
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
                                    <button type="button" class="btn btn-block btn-warning" data-toggle="modal" data-target="#suspendMmodal">
                                        <i class="fas fa-minus-circle"></i>
                                        <strong>Suspend</strong>
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-block btn-danger" data-toggle="modal" data-target="#banModal">
                                        <i class="fas fa-times-circle"></i>
                                        <strong>Ban</strong>
                                    </button>
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
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane active" id="user-info">
                                    @include('default_view::admin.parts.messages')
                                    <form action="" class="form-horizontal" method="POST" enctype="multipart/form-data">
                                        @csrf()
                                        @method('PUT')
                                        <div class="form-group row">
                                            <label for="avatar" class="col-sm-2 col-form-label">Avatar</label>
                                            <div class="col-sm-10">
                                                <input type="file" class="form-control-file" id="avatar" name="avatar" accept="image/*">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="username" class="col-sm-2 col-form-label">Username</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="username" name="username" value="{{ $user->username }}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="email" class="col-sm-2 col-form-label">Email</label>
                                            <div class="col-sm-10">
                                                <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="email_verified" class="col-sm-2 col-form-label">Email Verified</label>
                                            <div class="col-sm-10">
                                                <div class="custom-control custom-switch mt-2">
                                                    <input type="checkbox" name="email_verified" class="custom-control-input" id="email_verified" {!! $user->email_verified ? 'checked' : '' !!}>
                                                    <label class="custom-control-label" for="email_verified"></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="bio" class="col-sm-2 col-form-label">Bio</label>
                                            <div class="col-sm-10">
                                                <textarea name="bio" id="bio" cols="30" rows="3" class="form-control"></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="feature_tips" class="col-sm-2 col-form-label">Feature Tips</label>
                                            <div class="col-sm-10">
                                                <div class="custom-control custom-switch mt-2">
                                                    <input type="checkbox" class="custom-control-input" name="features[tips]" id="feature_tips" {!! $user->features['tips'] ? 'checked' : '' !!}>
                                                    <label class="custom-control-label" for="feature_tips"></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="feature_demo" class="col-sm-2 col-form-label">Feature Demo User</label>
                                            <div class="col-sm-10">
                                                <div class="custom-control custom-switch mt-2">
                                                    <input type="checkbox" class="custom-control-input" name="features[demo]" id="feature_demo" {!! $user->features['demo'] ? 'checked' : '' !!}>
                                                    <label class="custom-control-label" for="feature_demo"></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="gender" class="col-sm-2 col-form-label">Gender</label>
                                            <div class="col-sm-10">
                                                <select name="gender" id="gender" class="form-control">
                                                    @foreach($user->getGenders() as $key => $gender)
                                                        <option value="{{ $key }}" {!! $user->gender == $key ? 'selected' : '' !!}>{{ $gender }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="interested_in" class="col-sm-2 col-form-label">Interested In</label>
                                            <div class="col-sm-10">
                                                <select name="interested_in" id="interested_in" class="form-control">
                                                    @foreach($user->getInterestedIns() as $key => $interested_in)
                                                        <option value="{{ $key }}" {!! $user->interested_in == $key ? 'selected' : '' !!}>{{ $interested_in }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="type" class="col-sm-2 col-form-label">Type</label>
                                            <div class="col-sm-10">
                                                <select name="type" id="type" class="form-control">
                                                    @foreach($user->getTypes() as $key => $type)
                                                        <option value="{{ $key }}" {!! $user->type == $key ? 'selected' : '' !!}>{{ $type }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        @role('super-administrator')
                                            <div class="form-group row">
                                                <label for="role" class="col-sm-2 col-form-control">Role</label>
                                                <div class="col-sm-10">
                                                    <select name="role" class="form-control" id="role">
                                                        <option value=""></option>
                                                        @foreach($roles as $role)
                                                            <option value="{{ $role->name }}" {{ $user->role_ids && in_array($role->_id, $user->role_ids) ? 'selected' : '' }}>{{ $role->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        @endrole
                                        <div class="form-group row">
                                            <label for="status" class="col-sm-2 col-form-label">Status</label>
                                            <div class="col-sm-10">
                                                <select name="status" id="status" class="form-control">
                                                    @foreach($user->getStatuses() as $key => $status)
                                                        <option value="{{ $key }}" {!! $user->status == $key ? 'selected' : '' !!}>{{ $status }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="visibility" class="col-sm-2 col-form-label">Visibility</label>
                                            <div class="col-sm-10">
                                                <select name="visibility" id="visibility" class="form-control">
                                                    @foreach($user->getVisibilities() as $key => $visibility)
                                                        <option value="{{ $key }}" {!! $user->visibility == $key ? 'selected' : '' !!}>{{ $visibility }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="referral_id" class="col-sm-2 col-form-label">Referral User ID</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="referral_id" name="referral_id" value="{{ $user->referral_id }}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="promo_link" class="col-sm-2 col-form-label">Promo Link</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="promo_link" name="promo_link" value="{{ $user->promo_link }}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Created At</label>
                                            <div class="col-sm-10 mt-2">
                                                <p>{{ $user->created_at }}</p>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Updated At</label>
                                            <div class="col-sm-10 mt-2">
                                                <p>{{ $user->updated_at }}</p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 mt-3">
                                                <button type="submit" class="btn btn-md btn-primary col-md-1 float-right">Submit</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane" id="medias">
                                    medias
                                </div>
                                <div class="tab-pane" id="upload">
                                    upload
                                </div>
                                <div class="tab-pane" id="payment">
                                    <div class="content">
                                            <div class="container-fluid">
                                                <div class="row">
                                                    <h4>Credit Cards</h4>
                                                    <div class="col-12 table-responsive">
                                                    @php
                                                            $heads = [
                                                                'Username',
                                                                '',
                                                                'Status',
                                                                '',
                                                                'Expiration Month',
                                                                'Expiration Year',
                                                                'Created at',
                                                                '',
                                                            ];

                                                            $config = [
                                                                'processing' => true,
                                                                'serverSide' => true,
                                                                'pageLength' => config('core.admin.lists.page_count'),
                                                                'responsive' => true,
                                                                'lengthChange' => false,
                                                                'dom' => 'rtip',
                                                                'orderMulti' => false,
                                                                'bInfo' => false,
                                                                'searchCols' => [['search' => $user->username]],
                                                                'autoWidth' => false,
                                                                'ajax' => route('payment.admin.ajax.credit-card.index'),
                                                                'order' => [[1, 'asc']],
                                                                'columns' => [
                                                                    ['data' => 'link_to_user', 'orderData' => 1, 'target' => 1],
                                                                    ['data' => 'creator.username','visible' => false],
                                                                    ['data' => 'status_button','orderData' => 3, 'target' => 3],
                                                                    ['data' => 'status','visible' => false],
                                                                    ['data' => 'expire_month', 'orderable' => false],
                                                                    ['data' => 'expire_year', 'orderable' => false],
                                                                    ['data' => 'created_at'],
                                                                    ['data' => 'view_button', 'orderable' => false],
                                                                ],
                                                            ]
                                                        @endphp
                                                        <x-adminlte-datatable id="datatables" :heads="$heads" :config="$config">
                                                        </x-adminlte-datatable>
                                                    </div>


                                                    <h4>Tips</h4>
                                                    <div class="col-12 table-responsive">
                                                        @php
                                                            $heads = [
                                                                '',
                                                                'Creator',
                                                                '',
                                                                'Amount',
                                                                'Status',
                                                                '',
                                                                'Created at',
                                                                '',
                                                            ];

                                                        $config = [
                                                            'processing' => true,
                                                            'serverSide' => true,
                                                            'pageLength' => config('core.admin.lists.page_count'),
                                                            'responsive' => true,
                                                            'lengthChange' => false,
                                                            'bInfo' => false,
                                                            'dom' => 'rtip',
                                                            'orderMulti' => false,
                                                            'searchCols' => [['search' => $user->username]],
                                                            'autoWidth' => false,
                                                            'ajax' => route('payment.admin.ajax.tip.index'),
                                                            'order' => [[1, 'asc']],
                                                            'columns' => [
                                                                ['data' => 'user.username','visible' => false],
                                                                ['data' => 'link_to_creator', 'orderData' => 3, 'target' => 3],
                                                                ['data' => 'creator.username','visible' => false],
                                                                ['data' => 'amount'],
                                                                ['data' => 'status_button','orderData' => 6, 'target' => 6],
                                                                ['data' => 'status','visible' => false],
                                                                ['data' => 'created_at'],
                                                                ['data' => 'view_button', 'orderable' => false],
                                                            ],
                                                        ]
                                                        @endphp
                                                        <x-adminlte-datatable id="datatables1" :heads="$heads" :config="$config">
                                                        </x-adminlte-datatable>
                                                    </div>


                                                    <h4>Subscriptions</h4>
                                                    <div class="col-12 table-responsive">
                                                        @php
                                                            $heads = [
                                                                '',
                                                                'User',
                                                                '',
                                                                'Status',
                                                                '',
                                                                'Created at',
                                                                '',
                                                            ];

                                                        $config = [
                                                            'processing' => true,
                                                            'serverSide' => true,
                                                            'pageLength' => config('core.admin.lists.page_count'),
                                                            'responsive' => true,
                                                            'lengthChange' => false,
                                                            'bInfo' => false,
                                                            'dom' => 'rtip',
                                                            'orderMulti' => false,
                                                            'searchCols' => [['search' => $user->username]],
                                                            'autoWidth' => false,
                                                            'ajax' => route('payment.admin.ajax.subscription.index'),
                                                            'order' => [[1, 'asc']],
                                                            'columns' => [
                                                                ['data' => 'creator.username','visible' => false],
                                                                ['data' => 'link_to_user', 'orderData' => 2, 'target' => 2],
                                                                ['data' => 'user.username','visible' => false],
                                                                ['data' => 'status_button','orderData' => 5, 'target' => 5],
                                                                ['data' => 'status','visible' => false],
                                                                ['data' => 'created_at'],
                                                                ['data' => 'view_button', 'orderable' => false]
                                                            ],
                                                        ]
                                                        @endphp
                                                        <x-adminlte-datatable id="datatables2" :heads="$heads" :config="$config">
                                                        </x-adminlte-datatable>
                                                    </div>



                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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


