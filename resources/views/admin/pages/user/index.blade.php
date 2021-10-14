@extends('adminlte::page')
@section('title', 'Users')
@section('css')
    <link rel="stylesheet" href="{{ asset('admin/assets/css/user.css') }}">
@stop
@section('content_header')
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
@stop
@section('content')
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        @section('plugins.Datatables', true)
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            // do not submit when there are less than 3 characters
            // only check username, email, fullname for length
            // clean url when filter fields are empty

            var originalFormData = $('form#filter').serialize();
            $('input, select').blur(function() {

                const fieldValue = $(this).val();
                const cleanFieldName = $(this).attr('name').match(/\[(.*?)\]/)[1];
                const fieldWithLimit = ['username', 'email', 'full_name']

                if(validInput(fieldValue, cleanFieldName, fieldWithLimit)) {
                    $('#filter').submit();
                    return;
                } else {
                    $(this).val('');
                }

                if(isDirty(originalFormData)) {
                    let resetFilter = false;
                    let canSubmitForm = false;
                    $('input, select').each(function() {
                        const fieldValue = $(this).val();
                        const cleanFieldName = $(this).attr('name')?.match(/\[(.*?)\]/)[1];
                        if(validInput(fieldValue, cleanFieldName, fieldWithLimit)) {
                            canSubmitForm = true;
                            resetFilter = false;
                        } else {
                            resetFilter = true;
                        }
                    });



                    if(canSubmitForm) {
                        $('#filter').submit();
                    }

                    if(resetFilter && !canSubmitForm) {
                        window.location.href = '/user';
                    }
                }
            })



            function validInput(value, field_name = '', fieldWithLimit) {
                return (value.length > 2 || $.inArray(field_name, fieldWithLimit) === -1) && value.length !== 0;
            }

            function isDirty(originalFOrm) {
                return originalFOrm !== $('form#filter').serialize();
            }
        })
    </script>
@endsection
