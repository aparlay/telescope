@extends('default_view::admin.layouts.layout')
@section('title', 'Dashboard')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/assets/css/media.css') }}">
@endsection

@section('content')

<div class="content-wrapper">

    @include('default_view::admin.parts.breadcrumbs')

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <table class="table table-bordered mb-5">
                        <thead>
                        <tr>
                            <th scope="col">Cover</th>
                            <th scope="col">Created By</th>
                            <th scope="col">Description</th>
                            <th scope="col">Status</th>
                            <th scope="col">Created At</th>
                            <th scope="col">Likes</th>
                            <th scope="col">Visits</th>
                            <th scope="col">Sort Score</th>
                            <th scope="col"></th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($media_list as $media)
                                <tr>
                                    <td><img src="{{ $media->file }}" /></td>
                                    <td>
                                        <img class="table-avatar mr-1.5" src="{{ $media->creator['avatar'] }}" alt="{{ $media->creator['username'] }}"/>
                                        {{ $media->creator['username'] }}
                                    </td>
                                    <td>{{ $media->description }}</td>
                                    <td><span class="badge bg-{{ $media->status_text['color'] }}">{{ $media->status_text['text'] }}</span></td>
                                    <td>{{ $media->created_at }}</td>
                                    <td>{{ $media->like_count }}</td>
                                    <td>{{ $media->visit_count }}</td>
                                    <td>{{ $media->sort_score }}</td>
                                    <td>
                                        <a class="btn btn-primary btn-sm" href="/media/{{ $media->id }}" title="View"><i class="fas fa-eye"></i> View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @if(count($media_list))
                <div class="row">
                    <div class="col-6">
                        Showing <strong>{!! $media_list->firstItem() .'-' . $media_list->lastItem() !!}</strong> of <strong>{!! $media_list->total() !!}</strong> {!! $media_list->total() == 1 ? 'item' : 'items' !!}
                    </div>
                    @if($media_list->hasPages())
                        <div class="col-6 d-flex justify-content-end">
                            {{ $media_list->links() }}
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

</div>

@endsection
