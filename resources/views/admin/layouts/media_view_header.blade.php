<!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-8">
                    <h1 class="m-0">Media View
                        <a class="btn btn-default btn-sm border-primary col-md-1.5 ml-1 text-primary" name="" value="" href="{{url('pending/media/'.$prevPage )}}"><i class="fas fa-chevron-left"></i> <strong>Previous</strong></a>
                        <a class="btn btn-default btn-sm border-primary col-md-1.5 ml-1 text-primary" name="" value="" href="{{url('pending/media/'.$nextPage)}}"><strong>Next</strong> <i class="fas fa-chevron-right"></i></a>
                        <button class=" btn btn-sm btn-danger col-md-2" data-toggle="modal" data-target="#reprocessModel">
                            <i class="fas fa-exclamation-triangle"></i>
                            Reprocess
                        </button>
                        <button class=" btn btn-sm btn-warning col-md-2" id="mediaAlert" data-toggle="modal" data-target="#alert-modal" >
                            <i class="fas fa-minus-circle"></i>
                            Alert
                        </button>
                        <div class="dropdown btn-group col-md-1 mr-2 show">
                            <button class="btn btn-info btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-cloud-download-alt"></i>
                                <strong>Download</strong>
                                <b class="caret"></b>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                @if ($media->files_history)
                                    @foreach ($media->files_history as $file)
                                        <a class="dropdown-item" href="{{ route('core.admin.media.downloadOriginal', ['media' => $media->_id, 'hash' => $file['hash']]) }}">{{ $file['created_at'] }} - {{ $size->fileSize($file['size']) }}</a>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </h1>
                </div><!-- /.col -->
                <div class="col-sm-4">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Home</a></li>
                        <li class="breadcrumb-item">Media</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
