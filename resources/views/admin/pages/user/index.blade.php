@extends('default_view::admin.layouts.layout')
@section('title', 'Users')
@section('styles')
    <link rel="stylesheet" href="{{ asset('admin/assets/css/user.css') }}">
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
                                    <form action="" id="filter">
                                        <td>
                                            <input type="text" name="UserSearch[username]" class="form-control" value="{!! request()->UserSearch && isset(request()->UserSearch['username']) ? request()->UserSearch['username'] :  '' !!}">
                                        </td>
                                        <td>
                                            <input type="text" name="UserSearch[email]" class="form-control" value="{!! request()->UserSearch && isset(request()->UserSearch['email']) ? request()->UserSearch['email'] :  '' !!}">
                                        </td>
                                        <td>
                                            <input type="text" name="UserSearch[full_name]" class="form-control" value="{!! request()->UserSearch && isset(request()->UserSearch['full_name']) ? request()->UserSearch['full_name'] :  '' !!}">
                                        </td>
                                        <td>
                                            <select name="UserSearch[status]" id="" class="form-control">
                                                <option value=""></option>
                                                @foreach($userStatuses as $key => $value)
                                                    <option value="{{ $key }}" {!! (request()->UserSearch && isset(request()->UserSearch['status'])) && request()->UserSearch['status'] == $key ? 'selected' : '' !!}>{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="UserSearch[visibility]" class="form-control" value="{!! request()->UserSearch && isset(request()->UserSearch['visibility']) ? request()->UserSearch['visibility'] :  '' !!}">
                                        </td>
                                        <td>
                                            <input type="text" name="UserSearch[follower_count]" class="form-control" value="{!! request()->UserSearch && isset(request()->UserSearch['follower_count']) ? request()->UserSearch['follower_count'] :  '' !!}">
                                        </td>
                                        <td>
                                            <input type="text" name="UserSearch[like_count]" class="form-control" value="{!! request()->UserSearch && isset(request()->UserSearch['like_count']) ? request()->UserSearch['like_count'] :  '' !!}">
                                        </td>
                                        <td>
                                            <input type="text" name="UserSearch[media_count]" class="form-control" value="{!! request()->UserSearch && isset(request()->UserSearch['media_count']) ? request()->UserSearch['media_count'] :  '' !!}">
                                        </td>
                                        <input type="submit" class="d-none">
                                    </form>
                                </tr>
                                @if(!count($users))
                                    <tr>
                                        <td colspan="10" class="text-center">No users found.</td>
                                    </tr>
                                @endif
                                @foreach($users as $data)
                                    <tr>
                                        <td>{{ $data->username }}</td>
                                        <td><a href="mailto:{{ $data->email }}">{{ $data->email }}</a></td>
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
                                            <a class="btn btn-primary btn-block btn-sm" href="/user/{{ $data->_id }}" title="View"><i class="fas fa-eye"></i> View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @if(count($users))
                    <div class="row">
                        <div class="col-6">
                            Showing <strong>{!! $users->firstItem() .'-' . $users->lastItem() !!}</strong> of <strong>{!! $users->total() !!}</strong> {!! $users->total() == 1 ? 'item' : 'items' !!}
                        </div>
                        @if($users->hasPages())
                            <div class="col-6 d-flex justify-content-end">
                                {{ $users->links() }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            var originalFormData = $('form#filter').serialize();
            $('input, select').blur(function() {
                if(isDirty(originalFormData)) {
                    const field_value = $(this).val();
                    const clean_field_name = $(this).attr('name').match(/\[(.*?)\]/)[1];
                    const field_with_limit = ['username', 'email', 'full_name']

                    if(field_value.length > 2 || $.inArray(clean_field_name, field_with_limit) === -1) {
                        $('#filter').submit();
                        return;
                    }


                    let has_value = false;
                    $('input, select').each(function() {
                        if($(this).val().length === '') {
                            has_value = true;
                        }
                    });

                    //remove params
                    if(!has_value) {
                        window.location.href = '/user';
                    }
                }
            })

            function isDirty(originalFOrm) {
                return originalFOrm !== $('form#filter').serialize();
            }
        })
    </script>
@endsection
