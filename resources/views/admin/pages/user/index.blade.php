@extends('default_view::admin.layouts.layout')
@section('title', 'Users')
@section('styles')
    @livewireStyles
    <style>
        [x-cloak] { display: none !important; }
    </style>
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
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
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <table class="table table-bordered mb-5">
                            <thead>
                            <tr>
                                <th scope="col">Username</th>
                                <th scope="col">Email</th>
                                <th scope="col">Fullname</th>
                                <th scope="col">Status</th>
                                <th scope="col">Visibility</th>
                                <th scope="col">Followers</th>
                                <th scope="col">Likes</th>
                                <th scope="col">Medias</th>
                                <th scope="col">Created At</th>
                                <th scrope="col">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <form action="">
                                        <td>
                                            <input type="text" name="username" class="form-control" value="{!! request()->username !!}">
                                        </td>
                                        <td>
                                            <input type="text" name="email" class="form-control" value="{!! request()->email !!}">
                                        </td>
                                        <input type="submit" class="d-none">
                                    </form>
                                </tr>
                                @if(!count($users))
                                    <tr>
                                        <td colspan="7" class="text-center">No Users.</td>
                                    </tr>
                                @endif
                                @foreach($users as $data)
                                    <tr>
                                        <td>{{ $data->username }}</td>
                                        <td>{{ $data->email }}</td>
                                        <td>{{ $data->full_name }}</td>
                                        <td>
                                            <span class="badge bg-{{ $data->status_badge['color'] }}">
                                                {{ $data->status_badge['status'] }}
                                            </span>
                                            <br>
                                            <span class="badge bg-{{ $data->isverified_badge['color'] }}">
                                                {{ $data->isverified_badge['is_verified'] }}
                                            </span>
                                            <br>
                                            <span class="badge bg-{{ $data->gender_badge['color'] }}">
                                                {{ $data->gender_badge['gender'] }}
                                            </span>
                                        </td>
                                        <td>{{ $data->visibility }}</td>
                                        <td>{{ $data->follower_count }}</td>
                                        <td>{{ $data->like_count }}</td>
                                        <td>{{ $data->media_count }}</td>
                                        <td>{{ $data->created_at }}</td>
                                        <td>
                                            <a class="btn btn-primary btn-sm" href="/user/{{ $data->_id }}" title="View"><i class="fas fa-eye"></i> View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="col-12 d-flex justify-content-center">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @livewireScripts
@endsection
