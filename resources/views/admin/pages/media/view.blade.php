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
                            <form action="" class="form-horizontal" method="POST">
                                <div class="row">
                                    @csrf()
                                    @if ($media->status !== 3 && $media->status !== 7)
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-block btn-success">
                                            <i class="fas fa-minus-circle"></i>
                                            <strong>Approve</strong>
                                        </button>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-block btn-warning">
                                            <i class="fas fa-times-circle"></i>
                                            <strong>Denied</strong>
                                        </button>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-block btn-danger">
                                            <i class="fas fa-times-circle"></i>
                                            <strong>Delete + Alert</strong>
                                        </button>
                                    </div>
                                    @else
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-block btn-success" >
                                            <i class="fas fa-minus-circle"></i>
                                            <strong>Save</strong>
                                        </button>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-block btn-warning" >
                                            <i class="fas fa-times-circle"></i>
                                            <strong>Denied</strong>
                                        </button>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-block btn-danger" >
                                            <i class="fas fa-times-circle"></i>
                                            <strong>Delete + Alert</strong>
                                        </button>
                                    </div>
                                    @endif
                                </div>
                            </form>
                        </div>
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
                                    upload
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
@section('js')
<script>
    
    $('.skin_score_lable').click(function(){
        $('.skin_score_lable').removeClass('active');
        $(this).addClass('active');
    });
    
    $('.awesomeness_score_label').click(function(){
        $('.awesomeness_score_label').removeClass('active');
        $(this).addClass('active');
    });
    
</script>
@endsection
