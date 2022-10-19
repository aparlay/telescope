<!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-8">
                    <h1 class="m-0">Media View
                        <button class=" btn btn-sm btn-danger col-md-2" data-toggle="modal" data-target="#reprocessModel">
                            <i class="fas fa-exclamation-triangle"></i>
                            Reprocess
                        </button>
                        <button class="btn btn-sm btn-warning col-md-2" id="mediaAlert" data-toggle="modal" data-target="#alert-modal" >
                            <i class="fas fa-minus-circle"></i>
                            Alert
                        </button>
                        <div class="dropdown btn-group col-md-2 show">
                            <button class="btn btn-info btn-sm dropdown-toggle" type="button" id="dropdownMenuScore" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-cloud-download-alt"></i>
                                <strong>Promote</strong>
                                <b class="caret"></b>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuScore">
                                @foreach (range(1, 10) as $promotion)
                                    <a class="dropdown-item" href="{{ route('core.admin.media.recalculateSortScore', ['media' => $media->_id, 'promote' => $promotion]) }}">Add +{{ $promotion }} for a day</a>
                                @endforeach
                            </div>
                        </div>
                        <div class="dropdown btn-group col-md-2 mr-2 show">
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
