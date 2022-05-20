@extends('adminlte::page')

@section('title', 'Dashboard')

{{-- Enable Chartjs plugins --}}
@section('plugins.Chartjs', true)

@section('css')
    <link rel="stylesheet" href="/css/admin.css">
@stop
@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Dashboard</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('core.admin.dashboard') }}">Home</a></li>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@stop
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div id="accordion">
                <div class="card card-primary">
                    <div class="card-header">
                        <h4 class="card-title w-100">
                            <a class="d-block w-100" data-toggle="collapse" href="#collapseOne" aria-expanded="true">
                                Show/Hide Filter
                            </a>
                        </h4>
                    </div>
                    <div id="collapseOne" class="collapse" data-parent="#accordion" style="">
                        <div class="card-body">
                            <form action="{{ route('core.admin.ajax.dashboard.index') }}" id="filters">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="username">From Date</label>
                                            <input type="date" data-column="1" name="from_date" class="form-control from-date" id="fromDate" placeholder="Select Date">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="status">To Date</label>
                                            <input type="date" data-column="1" name="to_date" class="form-control" id="toDate" placeholder="Select Date">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="row d-flex justify-content-end">
                                            <div class="col-2">
                                                <label for="clearFilter"></label>
                                                <button type="button" id="clearFilter" class="btn btn-block btn-danger"><i class="fas fa-trash"></i> Clear</button>
                                            </div>
                                            <div class="col-2">
                                                <label for="submitFilter"></label>
                                                <button type="button" id="submitDate" class="btn btn-block btn-primary"><i class="fas fa-filter"></i> Filter</button>
                                            </div>
                                        </div>
                                    </div>
                                    <span id="alert"></span>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-none" data-chart="{{ $data_analytics }}" id="chart_data"></div>
            <div class="row">
                <div class="col-sm-12 col-md-6 col-lg-6">
                    <div class="card card-info">
                        <div class="card-header">
                            <div class="card-title">
                                User Analytics
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart">
                                <canvas id="user_chart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 col-lg-6">
                    <div class="card card-info">
                        <div class="card-header">
                            <div class="card-title">
                                User Durations
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart">
                                <canvas id="user_durations"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-6 col-lg-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <div class="card-title">
                                Media Analytics
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart">
                                <canvas id="media_chart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 col-lg-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <div class="card-title">
                                Media Visibility Analytics
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart">
                                <canvas id="media_visibility"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-6 col-lg-6">
                    <div class="card card-success">
                        <div class="card-header">
                            <div class="card-title">
                                Email Analytics
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart">
                                <canvas id="email_chart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('admin/assets/js/adminAnalytics.js') }}"></script>
$section('css')
    <style>
        .input-container input {
            border: none;
            box-sizing: border-box;
            outline: 0;
            padding: .75rem;
            position: relative;
            width: 100%;
        }

        input[type="date"]::-webkit-calendar-picker-indicator {
            background: transparent;
            bottom: 0;
            color: transparent;
            cursor: pointer;
            height: auto;
            left: 0;
            position: absolute;
            right: 0;
            top: 0;
            width: auto;
        }
    </style>
@endsection


