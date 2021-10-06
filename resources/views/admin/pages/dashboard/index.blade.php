@extends('default_view::admin.layouts.layout')
@section('title', 'Dashboard')
@section('styles')
    <link rel="stylesheet" href="{{ asset('admin/assets/css/chartjs/chart.min.css') }}">
@endsection
@section('content')
<div class="content-wrapper">

    @include('default_view::admin.parts.breadcrumbs')

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
</div>
@endsection
@section('scripts')
    <script src="{{ asset('admin/assets/js/chartjs/chart.min.js') }}"></script>
    <script src="{{ asset('admin/assets/js/adminAnalytics.js') }}"></script>
@endsection


