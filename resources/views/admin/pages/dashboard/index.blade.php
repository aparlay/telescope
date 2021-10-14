@extends('adminlte::page')

@section('title', 'Dashboard')

{{-- Enable Chartjs plugins --}}
@section('plugins.Chartjs', true)

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
@endsection


