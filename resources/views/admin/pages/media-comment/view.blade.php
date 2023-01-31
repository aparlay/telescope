@extends('adminlte::page')
@section('title', 'Media Comments')
@section('css')
    <link rel="stylesheet" href="/css/admin.css">
@stop
@section('plugins.Datatables', true)
@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Tip Details</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('core.admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('core.admin.media.index') }}">Medias</a></li>
                <li class="breadcrumb-item"><a
                        href="{{ route('core.admin.media.view', ['media' => $comment->mediaObj->_id]) }}">Media</a>
                </li>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
    <div class="content">
        <div class="container-fluid">
            <div class="row row h-100 justify-content-center align-items-center">

                <div class="col-md-4">
                    @include('default_view::admin.parts.messages')
                    <form method="POST"
                          action="{{ route('core.admin.media.view', ['media' => $comment->mediaObj->_id]) }}">
                        @csrf
                        @method('PUT')
                        <div class="card card-outline card-primary">
                            <div class="card-body box-profile ">
                                <ul class="list-group list-group-unbordered mb-3">
                                    <li class="list-group-item">
                                        <b>Text</b><br/>
                                        {{$comment->text}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Creator</b>
                                        <a class="float-right"
                                           href='{{$comment->creatorObj->admin_url}}'>{{$comment->creator['username']}}</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Media</b>
                                        <a class="float-right"
                                           href='{{$comment->mediaObj->admin_url}}'>{{$comment->mediaObj['file']}}</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Media creator</b>
                                        <a class="float-right"
                                           href='{{$comment->mediaObj->creatorObj->admin_url}}'>{{$comment->mediaObj->creatorObj->username}}</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Created Date</b>
                                        <span class="float-right">{{$comment->created_at}}</span>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Update Date</b>
                                        <span class="float-right">{{$comment->updated_at}}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="{{ asset('vendor/datatables-plugins/responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('admin/assets/js/adminDatatables.js') }}"></script>
@endsection
