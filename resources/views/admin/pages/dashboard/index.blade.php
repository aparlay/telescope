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
    </div><!-- /.row -->
@stop
@section('content')
    <div class="content bg-white">
        <livewire:dashboard/>
    </div>
@endsection
@section('js')
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


