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