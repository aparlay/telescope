@extends('adminlte::page')
@section('title', 'Setting View')
@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Setting View</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('core.admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('core.admin.setting.index') }}">Settings</a></li>
                <li class="breadcrumb-item">Setting View</li>
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
                    <form method="POST" action="">
                        @csrf
                        @method('PUT')
                        <div class="card card-outline card-primary">
                            <div class="card-body box-profile ">
                                <div class="form-group">
                                    <label for="group">Group</label>
                                    <select name="group" id="group" class="form-control" style="width: 100%">
                                        @foreach($groups as $group)
                                            <option value="{{ $group }}" {{ $setting->group === $group ? 'selected' : '' }}>{{ $group }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="title">Title</label>
                                    <input type="text" id="title" class="form-control" value="{{ $setting->title }}" name="title">
                                </div>
                                <div class="form-group">
                                    <label for="type">Value Type</label>
                                    <select name="type" id="type" class="form-control">
                                        @foreach(\Aparlay\Core\Admin\Models\Setting::getValueTypes() as $key => $valueTypes)
                                            <option value="{{ $key }}" {{ $setting->type == $key ? 'selected' : '' }}>{{ $valueTypes }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group" id="fieldContainer">
                                    <label for="value">Value</label>
                                    @switch($setting->type)
                                        @case(\Aparlay\Core\Admin\Models\Setting::VALUE_TYPE_JSON)
                                            <textarea name="value" id="value" rows="10" class="form-control valueType"
                                                  placeholder="{'key': 'value'} | string">{{ json_encode($setting->value, JSON_PRETTY_PRINT) }}</textarea>
                                            @break
                                        @case(\Aparlay\Core\Admin\Models\Setting::VALUE_TYPE_STRING)
                                            <input type="text" id="value" class="form-control valueType" value="{{ $setting->value }}">
                                            @break
                                        @case(\Aparlay\Core\Admin\Models\Setting::VALUE_TYPE_DATETIME)
                                            <div class="input-group datetime valueType" id="valuedatetime" data-target-input="nearest">
                                                <input type="text" name="value" value="{{ $setting->value }}" class="form-control valueType datetimepicker-input" data-target="#valuedatetime"/>
                                                <div class="input-group-append" data-target="#valuedatetime" data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                </div>
                                            </div>
                                            @break
                                        @case(\Aparlay\Core\Admin\Models\Setting::VALUE_TYPE_BOOLEAN)
                                            <div class="custom-control custom-switch bool valueType">
                                                <input type="checkbox" value="1" name="value" class="name custom-control-input valueType" id="customSwitches" {{ $setting->value ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="customSwitches"></label>
                                            </div>
                                            @break
                                        @case(\Aparlay\Core\Admin\Models\Setting::VALUE_TYPE_INTEGER)
                                            <input type="number" value="{{ $setting->value }}" id="value" name="value" class="int valueType form-control valueType">
                                            @break
                                    @endswitch
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
    <script src="{{ asset('admin/assets/js/adminSetting.js') }}"></script>
@endsection
