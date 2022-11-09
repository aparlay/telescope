@extends('adminlte::page')
@section('title', 'Media View')
@section('content')
@inject('dt', 'Aparlay\Core\Helpers\DT')
@inject('size', 'Aparlay\Core\Helpers\BladeHelper')
@inject('cdn', 'Aparlay\Core\Helpers\Cdn')
@include('default_view::admin.layouts.media_view_header')
@include('default_view::admin.parts.messages')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <div class="card card-primary card-outline">
                        <div class="ribbon-wrapper ribbon-xl">
                            <div class="ribbon bg-{{ $media->status_badge['color'] }}">
                                {{ $media->status_badge['status'] }}
                            </div>
                        </div>

                            <video width="100%" controls poster="{{ $media->cover_url }}" style="max-height:400px">
                                @if ($media->file)
                                    <source src="{{ $cdn->video($media->file) }}">
                                @endif
                                Your browser does not support the video tag.
                            </video>

                            <form action="" class="form-horizontal" method="POST">
                            <div class="card-body box-profile">
                                <div class="row">
                                    <div class="col-12">
                                        <ul class="list-group list-group-unbordered mb-3">
                                            <li class="list-group-item">
                                                <b>Skin Score</b>
                                                <div>
                                                    <input type="hidden" name="visibility" value="{{ $media->visibility }}">
                                                    <input type="hidden" name="is_music_licensed" value="{{ $media->is_music_licensed}}">
                                                    @foreach ($scoreTypes as $scoreType)
                                                        @foreach (\Aparlay\Core\Admin\Models\Media::getSkinScores() as $score)
                                                            @if($scoreType['type'] == 'skin')
                                                                <div id="skin_score_{{$score}}" class="btn-group btn-group-toggle skin_score_div" data-toggle="buttons" role="radiogroup">
                                                                    <label class="btn btn-outline-secondary skin_score_lable">
                                                                        <input type="radio" id="media_skin_score_{{$score}}" name="skin_score" value="{{ $score }}" data-index="{{ $score }}" autocomplete="off" @if($scoreType['score'] == $score) checked @endif>
                                                                        {{ $score }}
                                                                    </label>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @endforeach
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <b>Awesomeness Score</b>
                                                <div>
                                                    @foreach($scoreTypes as $scoreType)
                                                        @foreach (\Aparlay\Core\Admin\Models\Media::getAwesomenessScores() as $score)
                                                            @if($scoreType['type'] == 'awesomeness')
                                                                <div id="awesomeness_score_{{$score}}" class="btn-group btn-group-toggle awesomeness_score_div" data-toggle="buttons" role="radiogroup">
                                                                    <label class="btn btn-outline-secondary awesomeness_score_label">
                                                                        <input type="radio" id="media_awesomeness_score_{{$score}}" name="awesomeness_score" value="{{ $score }}" data-index="{{ $score }}" autocomplete="off" @if($scoreType['score'] == $score) checked @endif>
                                                                        {{ $score }}
                                                                    </label>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @endforeach
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <b>Beauty Score</b>
                                                <div>
                                                    @foreach($scoreTypes as $scoreType)
                                                        @foreach (\Aparlay\Core\Admin\Models\Media::getBeautyScores() as $score)
                                                            @if($scoreType['type'] == 'beauty')
                                                                <div id="beauty_score_{{$score}}" class="btn-group btn-group-toggle beauty_score_div" data-toggle="buttons" role="radiogroup">
                                                                    <label class="btn btn-outline-secondary beauty_score_label">
                                                                        <input type="radio" id="media_beauty_score_{{$score}}" name="beauty_score" value="{{ $score }}" data-index="{{ $score }}" autocomplete="off" @if($scoreType['score'] == $score) checked @endif>
                                                                        {{ $score }}
                                                                    </label>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @endforeach
                                                </div>
                                            </li>
                                        </ul>

                                        <div class="row">
                                            <div @class(['col-md-6', 'offset-3' => !$moderationQueueNotEmpty])>
                                                <button type="submit" id="mediaSave" class="btn btn-block btn-primary" name="status" value="{{ $media->status }}">
                                                    <i class="fas fa-check"></i>
                                                    <strong>Save Score</strong>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="row">
                                            @if ($hasPrev)
                                                <div class="col-md-6">
                                                    <a
                                                        href="{{ route('core.admin.media.moderation-queue.next', ['mediaId' => $media->_id, 'direction' => -1]) }}"
                                                        class="btn btn-info d-block">
                                                        <strong><i class="fa fa-arrow-left"></i> Previous </strong>
                                                    </a>
                                                </div>
                                            @endif

                                            @if($hasNext)
                                                    <div class="col-md-6">
                                                        <a
                                                            href="{{ route('core.admin.media.moderation-queue.next', ['mediaId' => $media->_id, 'direction' => 1]) }}"
                                                            class="btn btn-info d-block">
                                                            <strong>Next </strong><i class="fa fa-arrow-right"></i>
                                                        </a>
                                                    </div>
                                            @endif
                                        </div>
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    @csrf()
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-block btn-success" name="status" value="5"
                                            {{ $media->status !== \Aparlay\Core\Models\Enums\MediaStatus::DENIED->value &&
                                                $media->status !== \Aparlay\Core\Models\Enums\MediaStatus::COMPLETED->value &&
                                                $media->status !== \Aparlay\Core\Models\Enums\MediaStatus::IN_REVIEW->value ?
                                                 'disabled=disabled' : '' }} >
                                            <i class="fas fa-minus-circle"></i>
                                            <strong>Confirm</strong>
                                        </button>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-block btn-warning" name="status" value="6"
                                            {{ $media->status !== \Aparlay\Core\Models\Enums\MediaStatus::CONFIRMED->value &&
                                                    $media->status !== \Aparlay\Core\Models\Enums\MediaStatus::COMPLETED->value &&
                                                    $media->status !== \Aparlay\Core\Models\Enums\MediaStatus::IN_REVIEW->value ?
                                                     'disabled=disabled' : '' }}>
                                            <i class="fas fa-times-circle"></i>
                                            <strong>Deny</strong>
                                        </button>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-block btn-danger" id="deleteAlert" data-toggle="modal" data-target="#delete-alert-modal">
                                            <i class="fas fa-times-circle"></i>
                                            <strong>Delete + Alert</strong>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-items">
                                    <a href="#media-info" class="nav-link active" data-toggle="tab">Information</a>
                                </li>
                                <li class="nav-items">
                                    <a href="#upload" class="nav-link" data-toggle="tab">Upload</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane active" id="media-info">
                                    <form action="{{route('core.admin.media.update', ['media' => $media->_id])}}" class="form-horizontal" method="POST">
                                        @csrf()
                                        <div class="form-group row m-0">
                                            <label for="id" class="col-sm-2 col-form-label">ID</label>
                                            <div class="col-sm-10 mt-2">
                                                <p>{{ $media->_id }}</p>
                                            </div>
                                        </div>
                                        <div class="form-group row m-0">
                                            <label for="bio" class="col-sm-2 col-form-label">Description</label>
                                            <div class="col-sm-10">
                                                <textarea name="description" id="description" cols="30" rows="3" class="form-control">{{ $media->description }}</textarea>
                                            </div>
                                        </div>
                                        <div class="form-group row m-0">
                                            <label for="size" class="col-sm-2 col-form-label">Size</label>
                                            <div class="col-sm-10 mt-2">
                                                <p>{{ $size->fileSize($media->size) }}</p>
                                            </div>
                                        </div>
                                        <div class="form-group row m-0">
                                            <label for="length" class="col-sm-2 col-form-label">Length</label>
                                            <div class="col-sm-10 mt-2">
                                                <p>{{ $media->round_length }} Sec</p>
                                            </div>
                                        </div>
                                        <div class="form-group row m-0">
                                            <label for="likes" class="col-sm-2 col-form-label">Likes</label>
                                            <div class="col-sm-10 mt-2">
                                                <p>{{ $media->like_count }}</p>
                                            </div>
                                        </div>
                                        <div class="form-group row m-0">
                                            <label for="visits" class="col-sm-2 col-form-label">Visits</label>
                                            <div class="col-sm-10 mt-2">
                                                <p>{{ $media->visit_count }}</p>
                                            </div>
                                        </div>
                                        <div class="form-group row m-0">
                                            <label for="created-by" class="col-sm-2 col-form-label">Created By</label>
                                            <div class="col-sm-10 mt-2">
                                                <a href="/user/{{ $media->created_by }}">{{ $media->creator['username'] }}</a>
                                            </div>
                                        </div>
                                        <div class="form-group row m-0">
                                            <label for="status" class="col-sm-2 col-form-label">Status</label>
                                            <div class="col-sm-10">
                                                <select name="status" id="status" class="form-control">
                                                    @foreach(\App\Models\Media::getStatuses() as $key => $label)
                                                        <option value="{{ $key }}" {!! $media->status == $key ? 'selected' : '' !!}>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row m-0">
                                            <label for="feature_demo" class="col-sm-2 col-form-label">Show In Public Feed</label>
                                            <div class="col-sm-10">
                                                <div class="custom-control custom-switch mt-2">
                                                    <input type="checkbox" value="1" class="custom-control-input" name="visibility" id="visibility" {!! ($media->visibility == 1) ? 'checked' : '' !!}>
                                                    <label class="custom-control-label" for="visibility"></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row m-0">
                                            <label for="is_protected" class="col-sm-2 col-form-label">Delete Protected</label>
                                            <div class="col-sm-10">
                                                <div class="custom-control custom-switch mt-2">
                                                    <input type="checkbox" class="custom-control-input" name="is_protected" id="is_protected" {!! ($media->is_protected == true) ? 'checked' : '' !!}>
                                                    <label class="custom-control-label" for="is_protected"></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row m-0">
                                            <label for="is_music_licensed" class="col-sm-2 col-form-label">Music Licensed</label>
                                            <div class="col-sm-10">
                                                <div class="custom-control custom-switch mt-2">
                                                    <input type="checkbox" class="custom-control-input" name="is_music_licensed" id="is_music_licensed" {!! ($media->is_music_licensed == true) ? 'checked' : '' !!}>
                                                    <label class="custom-control-label" for="is_music_licensed"></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row m-0">
                                            <label for="updated-at" class="col-sm-2 col-form-label">Updated At</label>
                                            <div class="col-sm-10 mt-2">
                                                <p>{{ $media->updated_at }}</p>
                                            </div>
                                        </div>
                                        <div class="form-group row m-0">
                                            <label for="processing-log" class="col-sm-2 col-form-label">Processing Log</label>
                                            <div class="col-sm-10 mt-2">
                                                @isset($media->processing_log)
                                                    @foreach($media->processing_log as $log)
                                                        <p>{{ $log }}</p>
                                                    @endforeach
                                                @endisset
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 mt-3">
                                                <button type="submit" class="btn btn-md btn-primary col-md-1 float-right">Submit</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>


                                <div class="tab-pane" id="upload">
                                    <!-- The timeline -->
                                    <form method="post" action="{{ route('core.admin.media.reupload', ['media' => $media->_id]) }}" >
                                        <div class="flow-drop" data-upload-url="{{ route('core.admin.user.media.upload') }}" ondragenter="jQuery(this).addClass('flow-dragover');" ondragend="jQuery(this).removeClass('flow-dragover');" ondrop="jQuery(this).removeClass('flow-dragover');">
                                            Drop files here to upload
                                            <span>or</span>
                                            <a class="btn btn-md btn-outline-primary upload-btn flow-browse mt-3">
                                                select from your computer
                                            </a>
                                        </div>
                                        @csrf
                                        <div class="flow-list col-md-12 mt-3"></div>

                                        <input type="hidden" id="media_file" name="file">
                                        <input type="hidden" name="user_id" value="{{ $media->creator['_id'] }}">
                                        <input type="hidden" name="reupload_file" value="1" >

                                        <div class="row">

                                            <div class="col-4">
                                                <button class="btn btn-block btn-primary upload-video-button" name="create-button"><i class="fa fa-upload"></i><strong>Upload</strong></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="delete-alert-modal" class="fade modal" role="dialog" tabindex="-1" aria-hidden="true" aria-labelledby="delete-alert-modal-label">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="delete-alert-modal-label" class="modal-title">Media Alert</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="media-delete-alert-modal-form" class="form-vertical kv-form-bs4" action="{{route('core.admin.alert.store')}}" method="post" role="form">
                        @csrf()
                        <input type="hidden" name="status" value="{{ \Aparlay\Core\Models\Enums\AlertStatus::NOT_VISITED->value }}">
                        <input type="hidden" name="mediaStatus" value="{{ \Aparlay\Core\Models\Enums\MediaStatus::ADMIN_DELETED->value }}">
                        <div class="form-group highlight-addon field-media-delete-alert-modal-form-reason required">
                            <label class="has-star" for="media-delete-alert-modal-form-reason">Reason</label>
                            <div>
                                <input type="text" id="media-delete-alert-modal-form-reason" class="form-control" name="reason" placeholder="Type the message..." aria-required="true"></div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="form-group highlight-addon field-alert-type">
                                <input type="hidden" id="alert-type" class="form-control" name="type" value="{{ \Aparlay\Core\Models\Enums\AlertType::MEDIA_REMOVED->value }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="form-group highlight-addon field-alert-media_id">
                                <input type="hidden" id="alert-media_id" class="form-control" name="media_id" value="{{ $media->_id }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="form-group highlight-addon field-alert-user_id required">
                                <input type="hidden" id="alert-user_id" class="form-control" name="user_id" value="{{ $media->creatorObj->_id }}">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" form="media-delete-alert-modal-form">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert model -->
    <div id="alert-modal" class="fade modal" role="dialog" tabindex="-1" aria-hidden="true" aria-labelledby="alert-modal-label">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="delete-alert-modal-label" class="modal-title">Media Alert</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="alert-modal-form" class="form-vertical kv-form-bs4" action="{{route('core.admin.alert.store')}}" method="post" role="form">
                        @csrf()
                        <input type="hidden" name="status" value="{{ \Aparlay\Core\Models\Enums\AlertStatus::NOT_VISITED->value }}">
                        <input type="hidden" name="type" value="{{ \Aparlay\Core\Models\Enums\AlertType::MEDIA_NOTICED->value }}">
                        <div class="form-group highlight-addon field-media-delete-alert-modal-form-reason required">
                            <label class="has-star" for="alert-modal-form-reason">Reason</label>
                            <div>
                                <input type="text" id="alert-modal-form-reason" class="form-control" name="reason" placeholder="Type the message..." aria-required="true"></div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <input type="hidden" id="alert-media_id" class="form-control" name="media_id" value="{{ $media->_id }}">
                            <input type="hidden" id="alert-user_id" class="form-control" name="user_id" value="{{ $media->creatorObj->_id }}">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" form="alert-modal-form">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Reprocess media confirmation box -->
    <div class="modal fade" id="reprocessModel" tabindex="-1" role="dialog" aria-labelledby="reprocessModel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{route('core.admin.media.reprocess', ['media' => $media->_id])}}" method="post" role="form">
                @csrf()
                <div class="modal-content">
                <div class="modal-header bootstrap-dialog-header btn-warning">
                    <h5 class="modal-title" id="exampleModalLabel">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to reprocess this item?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal"><span class="fa fa-ban"></span> Cancel</button>
                    <button type="submit" class="btn btn-warning"><span class="fas fa-check"></span> Ok</button>
                </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('js')
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/js/flow/flow.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/js/uploadMedia.js') }}"></script>
<script src="{{ asset('admin/assets/js/media.js') }}"></script>
@stop
@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('admin/assets/css/uploadMedia.css') }}" >
@stop
