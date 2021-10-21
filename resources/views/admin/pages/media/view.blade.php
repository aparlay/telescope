@extends('adminlte::page')
@section('title', 'Media View')
@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-7">
                    <h1 class="m-0">Media View
                        <a class="btn btn-default btn-sm border-primary col-md-1.5 ml-1 text-primary" name="" value="" href=""><i class="fas fa-chevron-left"></i> <strong>Previous</strong></a>
                        <a class="btn btn-default btn-sm border-primary col-md-1 ml-1 text-primary" name="" value="" href=""><strong>Next</strong> <i class="fas fa-chevron-right"></i></a>
                        <button class="ml-1 btn btn-sm btn-danger col-md-2">
                            <i class="fas fa-exclamation-triangle"></i>
                            Reprocessing
                        </button>
                        <button class="ml-1 btn btn-sm btn-warning col-md-2">
                            <i class="fas fa-minus-circle"></i>
                            Alert
                        </button>
                        <button class="ml-1 btn btn-sm btn-info col-md-2">
                            <i class="fas fa-cloud-download-alt"></i>
                            Download
                        </button>                            
                    </h1>
                    <br>
                    <div id="app">
                    @if (Session::get('success'))
                    <div class="alert alert-success alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>	
                            <strong>{{ Session::get('success') }}</strong>
                    </div>
                    @endif
                    @if (Session::get('danger'))
                    <div class="alert alert-danger alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>	
                            <strong>{{ Session::get('danger') }}</strong>
                    </div>
                    @endif
                    </div>
                </div><!-- /.col -->
                <div class="col-sm-5">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item">Media</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
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
                            <video width="100%" controls poster="{{ $media->url('cover') . $media->filename . '.jpg' }}" style="max-height:400px">
                                <source src="{{ $media->url() . $media->file }}">
                                Your browser does not support the video tag.
                            </video>

                            <form action="" class="form-horizontal" method="POST">
                            <div class="card-body box-profile">
                                <ul class="list-group list-group-unbordered mb-3">
                                    <li class="list-group-item">
                                        <b>Skin Score</b>
                                        <div>
                                        @foreach ($scoreTypes as $scoreType)
                                            @foreach ($skinScore as $score)
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
                                        @foreach ($scoreTypes as $scoreType)
                                            @foreach ($awesomenessScore as $score)
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
                                </ul>
                                <div class="row">
                                    @csrf()
                                    @if ($media->status !== 3 && $media->status !== 7)
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-block btn-success">
                                            <i class="fas fa-minus-circle"></i>
                                            <strong>Approve</strong>
                                        </button>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-block btn-warning" name="status" value="6">
                                            <i class="fas fa-times-circle"></i>
                                            <strong>Denied</strong>
                                        </button>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-block btn-danger" data-toggle="modal" data-target="#delete-alert-modal">
                                            <i class="fas fa-times-circle"></i>
                                            <strong>Delete + Alert</strong>
                                        </button>
                                    </div>
                                    @else
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-block btn-success">
                                            <i class="fas fa-minus-circle"></i>
                                            <strong>Save</strong>
                                        </button>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-block btn-warning"  name="status" value="6">
                                            <i class="fas fa-times-circle"></i>
                                            <strong>Denied</strong>
                                        </button>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-block btn-danger"  data-toggle="modal" data-target="#delete-alert-modal">
                                            <i class="fas fa-times-circle"></i>
                                            <strong>Delete + Alert</strong>
                                        </button>
                                    </div>
                                    @endif
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
                                    <form action="" class="form-horizontal" method="POST">
                                        @csrf()
                                        <div class="form-group row">
                                            <label for="id" class="col-sm-2 col-form-label">ID</label>
                                            <div class="col-sm-10 mt-2">
                                                <p>{{ $media->_id }}</p>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="bio" class="col-sm-2 col-form-label">Description</label>
                                            <div class="col-sm-10">
                                                <textarea name="description" id="description" cols="30" rows="3" class="form-control">{{ $media->description }}</textarea>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="size" class="col-sm-2 col-form-label">Size</label>
                                            <div class="col-sm-10 mt-2">
                                                <p>{{ $media->size }}</p>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="length" class="col-sm-2 col-form-label">Length</label>
                                            <div class="col-sm-10 mt-2">
                                                <p>{{ $media->length }}</p>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="likes" class="col-sm-2 col-form-label">Likes</label>
                                            <div class="col-sm-10 mt-2">
                                                <p>{{ $media->like_count }}</p>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="visits" class="col-sm-2 col-form-label">Visits</label>
                                            <div class="col-sm-10 mt-2">
                                                <p>{{ $media->visit_count }}</p>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="created-by" class="col-sm-2 col-form-label">Created By</label>
                                            <div class="col-sm-10 mt-2">
                                                <a href="/user/{{ $media->created_by }}">{{ $media->creator['username'] }}</a>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="status" class="col-sm-2 col-form-label">Status</label>
                                            <div class="col-sm-10">
                                                <select name="status" id="status" class="form-control">
                                                    @foreach($media->getStatuses() as $key => $status)
                                                        <option value="{{ $key }}" {!! $media->status == $key ? 'selected' : '' !!}>{{ $status }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="feature_demo" class="col-sm-2 col-form-label">Show In Public Feed</label>
                                            <div class="col-sm-10">
                                                <div class="custom-control custom-switch mt-2">
                                                    <input type="checkbox" class="custom-control-input" name="visibility" id="visibility" {!! ($media->visibility == 1) ? 'checked' : '' !!}>
                                                    <label class="custom-control-label" for="visibility"></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="feature_demo" class="col-sm-2 col-form-label">Music Licensed</label>
                                            <div class="col-sm-10">
                                                <div class="custom-control custom-switch mt-2">
                                                    <input type="checkbox" class="custom-control-input" name="is_music_licensed" id="is_music_licensed" {!! ($media->is_music_licensed == true) ? 'checked' : '' !!}>
                                                    <label class="custom-control-label" for="is_music_licensed"></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="updated-at" class="col-sm-2 col-form-label">Skin Score</label>
                                            <div class="col-sm-10 mt-2">
                                            @foreach ($scoreTypes as $scoreType)
                                                @if($scoreType['type'] == 'skin')
                                                    <p>{{ $scoreType['score'] }}</p>
                                                @endif
                                            @endforeach
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="updated-at" class="col-sm-2 col-form-label">Awesomeness Score</label>
                                            <div class="col-sm-10 mt-2">
                                            @foreach ($scoreTypes as $scoreType)
                                                @if($scoreType['type'] == 'awesomeness')
                                                    <p>{{ $scoreType['score'] }}</p>
                                                @endif
                                            @endforeach
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="updated-at" class="col-sm-2 col-form-label">Updated At</label>
                                            <div class="col-sm-10 mt-2">
                                                <p>{{ $media->updated_at }}</p>
                                            </div>
                                        </div>
                                        <div class="form-group row">
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
                                    <form method="post" action="{{url('media/'.$media->_id)}}" >
                                        <div class="flow-drop" data-upload-url="{{url('user/upload-media')}}" ondragenter="jQuery(this).addClass('flow-dragover');" ondragend="jQuery(this).removeClass('flow-dragover');" ondrop="jQuery(this).removeClass('flow-dragover');">
                                            Drop files here to upload or <a class="flow-browse"><u>select from your computer</u></a>
                                        </div>
                                        @csrf
                                        <div class="flow-list col-md-12 mt-3"></div>

                                        <input type="hidden" id="media-file" name="file">
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
                    <form id="media-delete-alert-modal-form" class="form-vertical kv-form-bs4" action="{{url('/alert/create')}}" method="post" role="form">
                        @csrf()
                        <div class="form-group highlight-addon field-media-delete-alert-modal-form-reason required">
                            <label class="has-star" for="media-delete-alert-modal-form-reason">Reason</label>
                            <div>
                                <input type="text" id="media-delete-alert-modal-form-reason" class="form-control" name="reason" placeholder="Type the message..." aria-required="true" data-krajee-typeahead="typeahead_7864e59a"></div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="form-group highlight-addon field-alert-type">
                                <input type="hidden" id="alert-type" class="form-control" name="type" value="20">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="form-group highlight-addon field-alert-media_id">
                                <input type="hidden" id="alert-media_id" class="form-control" name="media_id" value="{{ $media->_id }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="form-group highlight-addon field-alert-user_id required">
                                <input type="hidden" id="alert-user_id" class="form-control" name="user_id" value="{{ $media->created_by }}">
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
@endsection
@section('script')
<script src="{{ URL::asset('admin/assets/js/flow/flow.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/adminUploadMedia.js') }}"></script>
<script src="{{ asset('admin/assets/js/adminMedia.js') }}"></script>
@endsection
@section('css')
    <style>
        /* Uploader: Drag & Drop */
        .flow-error {display:none; font-style:italic;}
        .flow-drop {padding: 30px 15px; text-align:center; color:#666; font-weight:bold;background-color:#eee; border:2px dashed #aaa; border-radius:10px; margin-top:20px; z-index:9999; display:none;}
        .flow-dragover {padding:30px; color:#555; background-color:#ddd; border:1px solid #999;}
        /* Uploader: Progress bar */
        .flow-progress {margin:30px 0 30px 0; width:100%; display:none;}
        .progress-container {height:7px; background:#9CBD94; position:relative; }
        .progress-bar {position:absolute; top:0; left:0; bottom:0; background:#45913A; width:0;}
        .progress-text {line-height:9px; padding-left:10px;}
        .progress-pause {padding:0 0 0 7px;}
        .progress-resume-link {display:none;}
        .is-paused .progress-resume-link {display:inline;}
        .is-paused .progress-pause-link {display:none;}
        .is-complete .progress-pause {display:none;}

        /* Uploader: List of items being uploaded */
        .flow-list {overflow:auto; margin-right:-20px; display:none;}
        .uploader-item {width:148px; height:90px; background-color:#666; position:relative; border:2px solid black; float:left; margin:0 6px 6px 0;}
        .uploader-item-thumbnail {width:100%; height:100%; position:absolute; top:0; left:0;}
        .uploader-item img.uploader-item-thumbnail {opacity:0;}
        .uploader-item-creating-thumbnail {padding:0 5px; color:white;}
        .uploader-item-title {position:absolute; line-height:11px; padding:3px 50px 3px 5px; bottom:0; left:0; right:0; color:white; background-color:rgba(0,0,0,0.6); min-height:27px;}
        .uploader-item-status {position:absolute; bottom:3px; right:3px;}
        .row {margin-top: 10px;}
        /* Uploader: Hover & Active status */
        .uploader-item:hover, .is-active .uploader-item {border-color:#4a873c; cursor:pointer; }
        .uploader-item:hover .uploader-item-title, .is-active .uploader-item .uploader-item-title {background-color:rgba(74,135,60,0.8);}
        /* Uploader: Error status */
        .is-error .uploader-item:hover, .is-active.is-error .uploader-item {border-color:#900;}
        .is-error .uploader-item:hover .uploader-item-title, .is-active.is-error .uploader-item .uploader-item-title {background-color:rgba(153,0,0,0.6);}
        .is-error .uploader-item-creating-thumbnail {display:none;}
    </style>
@stop