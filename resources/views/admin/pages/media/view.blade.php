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
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <video width="470" height="305" controls>
                                    <source src="{{URL::asset("/images/upload/$media->file")}}" type="video/mp4">
                                </video>
                            </div>
                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Skin Score</b>
                                    <div>
                                        @for ($i = 1; $i < 11; $i++)
                                            <div id="media-skin_score" class="btn-group btn-group-toggle" data-toggle="buttons" role="radiogroup">
                                                <label class="btn btn-outline-secondary">
                                                    <input type="radio" id="media-skin-score--{{$i}}" name="score" value="{{ $media->score }}" data-index="0" autocomplete="on" @if($media->scores == "") checked @endif>
                                                    {{ $i }}
                                                </label>
                                        @endfor
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <b>Awesomeness Score</b>
                                    <div>
                                        @for ($i = 1; $i < 11; $i++)
                                            <div id="media-skin_score" class="btn-group btn-group-toggle" data-toggle="buttons" role="radiogroup">
                                                <label class="btn btn-outline-secondary">
                                                    <input type="radio" id="media-skin-score--0" name="sort_score" value="{{ $media->sort_score }}" data-index="0" autocomplete="off" @if($media->sort_score == "5") selected @endif>
                                                    {{ $i }}
                                                </label>
                                            
                                        @endfor
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <div class="row">
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-block btn-success" data-toggle="modal" data-target="#suspendMmodal">
                                        <i class="fas fa-minus-circle"></i>
                                        <strong>Save</strong>
                                    </button>
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-block btn-warning" data-toggle="modal" data-target="#banModal">
                                        <i class="fas fa-times-circle"></i>
                                        <strong>Denied</strong>
                                    </button>
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-block btn-danger" data-toggle="modal" data-target="#banModal">
                                        <i class="fas fa-times-circle"></i>
                                        <strong>Delete + Alert</strong>
                                    </button>
                                </div>
                            </div>
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
                                                    <input type="checkbox" class="custom-control-input" id="visibility" {!! $media->visibility ? 'checked' : '' !!}>
                                                    <label class="custom-control-label" for="visibility"></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="feature_demo" class="col-sm-2 col-form-label">Music Licensed</label>
                                            <div class="col-sm-10">
                                                <div class="custom-control custom-switch mt-2">
                                                    <input type="checkbox" class="custom-control-input" id="is_music_licensed" {!! $media->is_music_licensed ? 'checked' : '' !!}>
                                                    <label class="custom-control-label" for="is_music_licensed"></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="updated-at" class="col-sm-2 col-form-label">Skin Score</label>
                                            <div class="col-sm-10 mt-2">
                                                <p>{{ $media->scores }}</p>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="updated-at" class="col-sm-2 col-form-label">Awesomeness Score</label>
                                            <div class="col-sm-10 mt-2">
                                                <p>{{ $media->sort_score }}</p>
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

