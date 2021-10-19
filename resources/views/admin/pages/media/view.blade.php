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
                            <video class="section" controls>
                                <source src="{{URL::asset("/storage/app/upload/$media->file")}}" type="video/mp4">
                            </video>
                            <form action="" class="form-horizontal" method="POST">
                            <div class="card-body box-profile">
                                <ul class="list-group list-group-unbordered mb-3">
                                    <li class="list-group-item">
                                        <b>Skin Score</b>
                                        <div>
                                        @foreach ($score_types as $scoreType)
                                            @foreach ($skin_score as $score)
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
                                        @foreach ($score_types as $scoreType)
                                            @foreach ($awesomeness_score as $score)
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
                                        <button type="submit" class="btn btn-block btn-warning">
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
                                        <button type="submit" class="btn btn-block btn-warning"  data-toggle="modal" data-target="#delete-alert-modal">
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
                                            @foreach ($score_types as $scoreType)
                                                @if($scoreType['type'] == 'skin')
                                                    <p>{{ $scoreType['score'] }}</p>
                                                @endif
                                            @endforeach
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="updated-at" class="col-sm-2 col-form-label">Awesomeness Score</label>
                                            <div class="col-sm-10 mt-2">
                                            @foreach ($score_types as $scoreType)
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
                                    <form method="post" action="{{url('user/upload-media1')}}" >
                                        <div class="flow-drop" data-upload-url="{{url('user/upload-media')}}" ondragenter="jQuery(this).addClass('flow-dragover');" ondragend="jQuery(this).removeClass('flow-dragover');" ondrop="jQuery(this).removeClass('flow-dragover');">
                                            Drop files here to upload or <a class="flow-browse"><u>select from your computer</u></a>
                                        </div>

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
                    <form id="media-delete-alert-modal-form" class="form-vertical kv-form-bs4" action="/alert/create" method="post" role="form">
                        @csrf()
                        <div class="form-group highlight-addon field-media-delete-alert-modal-form-reason required">
                            <label class="has-star" for="media-delete-alert-modal-form-reason">Reason</label>
                            <div>
                                <input type="text" id="media-delete-alert-modal-form-reason" class="form-control" name="Alert[reason]" placeholder="Type the message..." aria-required="true" data-krajee-typeahead="typeahead_7864e59a"></div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="form-group highlight-addon field-alert-type">
                                <input type="hidden" id="alert-type" class="form-control" name="Alert[type]" value="20">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="form-group highlight-addon field-alert-media_id">
                                <input type="hidden" id="alert-media_id" class="form-control" name="Alert[media_id]" value="{{ $media->_id }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="form-group highlight-addon field-alert-user_id required">
                                <input type="hidden" id="alert-user_id" class="form-control" name="Alert[user_id]" value="{{ $media->created_by }}">
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="{{ URL::asset('admin/assets/js/flow/flow.min.js') }}"></script>
<script>
    $('.skin_score_lable').click(function(){
        $('.skin_score_lable').removeClass('active');
        $(this).addClass('active');
    });
    
    $('.awesomeness_score_label').click(function(){
        $('.awesomeness_score_label').removeClass('active');
        $(this).addClass('active');
    });

    $(document).ready(function() {
        var flow = new Flow({
            target: window.location.href,
        });
        // Flow.js isn't supported, fall back on a different method
        if(!flow.support) location.href = '/user/index';
        flow.assignBrowse(document.getElementById('media-file'));
        $('.upload-video-button').prop("disabled",true);
        
        $('.flow-drop').show();
        var csrfToken = $('meta[name="csrf-token"]').attr("content");
        var r = new Flow({
            singleFile: true,
            target: $('.flow-drop').data('upload-url'),
            chunkSize: 1024*1024*5,
            query : {
                "_token" : csrfToken
            },
            testChunks: false
        });
        // Flow.js isn't supported, fall back on a different method
        if (!r.support) {
            $('.flow-error').show();
            return ;
        }
        // Show a place for dropping/selecting files
        $('.flow-drop').show();
        r.assignDrop($('.flow-drop')[0], false, false, {accept: 'video/*'});
        r.assignBrowse($('.flow-browse')[0], false, false, {accept: 'video/*'});

        // Handle file add event
        r.on('fileAdded', function(file){
            if (file.size > (1024*1024*1024)) {
                alert("Maximum file size is 1G!");
                return false;
            }
            if (!file.file.type.match('video.*')) {
                alert("Only video files are allowed!");
                return false;
            }
            // Show progress bar
            $('.flow-progress, .flow-list').show();
            // Add the file to the list
            $('.flow-list').html(
                '<p class="flow-file flow-file-'+file.uniqueIdentifier+'">' +
                'Uploading <strong><span class="flow-file-name"></span></strong> ' +
                '<span class="flow-file-size"></span> ' +
                '<strong><span class="flow-file-progress">' +
                '</span></strong> ' +
                '</p>'
            );
            var $self = $('.flow-file-'+file.uniqueIdentifier);
            $self.find('.flow-file-name').text(file.name);
            $self.find('.flow-file-size').text(readablizeBytes(file.size));
        });
        r.on('filesSubmitted', function(file) {
            r.upload();
        });
        r.on('complete', function(){
            // Hide pause/resume when the upload has completed
            $('.flow-progress .progress-resume-link, .flow-progress .progress-pause-link').hide();
            $('.upload-video-button').prop("disabled", false);
        });
        r.on('fileSuccess', function(file,message){
            var $self = $('.flow-file-'+file.uniqueIdentifier);
            // Reflect that the file upload has completed
            $self.find('.flow-file-progress').text('(completed)');
            $self.find('.flow-file-pause, .flow-file-resume').remove();
            var response = JSON.parse(message);
            $('#media-file').val(response.data.file);
        });
        r.on('fileError', function(file, message){
            // Reflect that the file upload has resulted in error
            $('.flow-file-'+file.uniqueIdentifier+' .flow-file-progress').html('(file could not be uploaded: '+message+')');
        });
        r.on('fileProgress', function(file){
            // Handle progress for both the file and the overall upload
            $('.flow-file-'+file.uniqueIdentifier+' .flow-file-progress')
                .html(Math.floor(file.progress()*100) + '% '
                    + readablizeBytes(file.averageSpeed) + '/s '
                    + secondsToStr(file.timeRemaining()) + ' remaining') ;
            $('.progress-bar').css({width:Math.floor(r.progress()*100) + '%'});
        });
        r.on('uploadStart', function(){
            // Show pause, hide resume
            $('.flow-progress .progress-resume-link').hide();
            $('.flow-progress .progress-pause-link').show();
        });
        r.on('catchAll', function() {
            console.log.apply(console, arguments);
        });
        window.r = {
            upload: function() {
                r.resume();
            },
            flow: r
        };
    });

    function readablizeBytes(bytes) {
        var s = ['bytes', 'kB', 'MB', 'GB', 'TB', 'PB'];
        var e = Math.floor(Math.log(bytes) / Math.log(1024));
        return (bytes / Math.pow(1024, e)).toFixed(2) + " " + s[e];
    }

    function secondsToStr (temp) {
        function numberEnding (number) {
            return (number > 1) ? 's' : '';
        }
        var years = Math.floor(temp / 31536000);
        if (years) {
            return years + ' year' + numberEnding(years);
        }
        var days = Math.floor((temp %= 31536000) / 86400);
        if (days) {
            return days + ' day' + numberEnding(days);
        }
        var hours = Math.floor((temp %= 86400) / 3600);
        if (hours) {
            return hours + ' hour' + numberEnding(hours);
        }
        var minutes = Math.floor((temp %= 3600) / 60);
        if (minutes) {
            return minutes + ' minute' + numberEnding(minutes);
        }
        var seconds = temp % 60;
        return seconds + ' second' + numberEnding(seconds);
    }

</script>
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