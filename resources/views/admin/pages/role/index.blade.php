@extends('adminlte::page')
@section('title', 'Role')
@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Roles</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('core.admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="#">Roles</a></li>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@stop
@section('content')
    @include('default_view::admin.parts.messages')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-8 offset-2">
                    <div id="accordion">
                        @forelse($roles as $key => $role)
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title" style="line-height: 1.8">
                                        <a href="#collapse{{ $key }}" class="d-block w-100" data-toggle="collapse" aria-expanded="true">
                                            {{ ucwords(str_replace('-', ' ', $role->name)) }}
                                        </a>
                                    </h4>
                                    <div class="card-tools">
                                        <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#attach{{ $key }}">Attach permission</button>
                                        <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#remove{{ $key }}">Remove permission</button>
                                    </div>
                                </div>
                                <div class="collapse" id="collapse{{ $key }}" data-parent="#accordion">
                                    <div class="card-body">
                                        <h4>Permissions</h4>
                                        <div class="col-12">
                                            @php
                                                $heads = [
                                                    'Name',
                                                    'Guard',
                                                ];

                                                $config = [
                                                    'responsive' => true,
                                                    'lengthChange' => false,
                                                    'orderMulti' => false,
                                                    'autoWidth' => false,
                                                    'data' => $role->permissions,
                                                    'columns' => [
                                                        ['data' => 'name'],
                                                        ['data' => 'guard_name'],
                                                    ]
                                                ];
                                            @endphp
                                            <x-adminlte-datatable id="datatables{{ $key }}" :heads="$heads" :config="$config">
                                            </x-adminlte-datatable>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="attach{{ $key }}" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <form action="{{ route('core.admin.role.update', ['role' => $role->_id ]) }}" method="POST">
                                            @csrf
                                            <input type="hidden" value="attach" name="action">
                                            <div class="modal-header bg-primary">
                                                <h5 class="modal-title" id="exampleModalLiveLabel">Attach Permission to {{ ucwords(str_replace('-', ' ', $role->name)) }}</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            @if(!$role->unassigned_permission->isEmpty())
                                                                <label for="">Unassigned Permissions</label>
                                                                <select name="permissions[]" class="form-control select2" multiple="multiple" style="width: 100%">
                                                                    @foreach($role->unassigned_permission as $unassigned)
                                                                        <option value="{{ $unassigned->name }}">{{ $unassigned->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            @else
                                                                <p>No unassigned permissions.</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                                                @if(!$role->unassigned_permission->isEmpty())
                                                    <button type="submit" class="btn btn-primary">Attach</button>
                                                @endif
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div id="remove{{ $key }}" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <form action="{{ route('core.admin.role.update', ['role' => $role->_id ]) }}" method="POST">
                                            @csrf
                                            <input type="hidden" value="remove" name="action">
                                            <div class="modal-header bg-danger">
                                                <h5 class="modal-title" id="exampleModalLiveLabel">Remove Permission to {{ ucwords(str_replace('-', ' ', $role->name)) }}</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            @if(!$role->permissions->isEmpty())
                                                                <label for="">Assigned Permissions</label>
                                                                <select name="permissions[]" class="form-control select2" multiple="multiple" style="width: 100%">
                                                                    @foreach($role->permissions as $permission)
                                                                        <option value="{{ $permission->name }}">{{ $permission->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            @else
                                                                <p>No assigned permissions.</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                                                @if(!$role->permissions->isEmpty())
                                                    <button type="submit" class="btn btn-danger">Remove</button>
                                                @endif
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center">
                                <strong>No Roles.</strong>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('plugins.Select2', true)
@section('plugins.Datatables', true)

@section('js')
    <script>
        $('.select2').select2()
    </script>
@endsection
