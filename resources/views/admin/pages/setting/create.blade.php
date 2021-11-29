@extends('adminlte::page')
@section('title', 'Add Setting')
@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/css/adminStyles.css') }}" >
@endsection
@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Add Setting</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('core.admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('core.admin.setting.index') }}">Settings</a></li>
                <li class="breadcrumb-item">Add Setting</li>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@stop
@section('content')
    @include('default_view::admin.parts.messages')
    <div class="content">
        <div class="container-fluid">
            <div class="row row h-100 justify-content-center align-items-center">

                <div class="col-md-4">
                    <form method="POST" action="{{ route('core.admin.setting.store') }}">
                        @csrf
                        <div class="card card-outline card-primary">
                            <div class="card-body box-profile ">
                                <div class="form-group">
                                    <label for="group">Group</label>
                                    <select name="group" id="group" class="form-control" style="width: 100%">
                                        @foreach($groups as $group)
                                            <option value="{{ $group }}">{{ $group }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="title">Title</label>
                                    <input type="text" id="title" class="form-control" name="title">
                                </div>
                                <div class="form-group">
                                    <label for="type">Value Type</label>
                                    <select name="type" id="type" class="form-control">
                                        @foreach(\Aparlay\Core\Admin\Models\Setting::getValueTypes() as $key => $valueTypes)
                                            <option value="{{ $key }}">{{ $valueTypes }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group" id="fieldContainer">
                                    <label for="value">Value</label>
                                    <input type="text" id="value" name="value" class="string valueType form-control">
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary float-right">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection
@section('plugins.Select2', true)
@section('js')
    <script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="{{ asset('vendor/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.js') }}"></script>
    <script src="{{ asset('admin/assets/js/adminSetting.js') }}"></script>
@endsection
