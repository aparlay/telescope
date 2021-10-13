@extends('default_view::admin.layouts.layout')
@section('title', 'Media View')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Media View
                            <button class="ml-4 btn btn-sm btn-danger col-md-1">
                                <i class="fas fa-minus-circle"></i>
                                Alert
                            </button>
                        </h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
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
                    <div class="col-md-3">
                        <div class="card card-primary card-outline">
                            <div class="ribbon-wrapper ribbon-xl">
                                <div class="ribbon ">
                                    
                                </div>
                            </div>
                            <div class="card-body box-profile">
                                <div class="text-center">
                                    
                                </div>
                                <h3 class="text-center"></h3>
                                <p class="text-muted text-center"></p>
                                <ul class="list-group list-group-unbordered mb-3">
                                    <li class="list-group-item">
                                        <b>Medias</b>
                                        <a class="float-right"></a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Followers</b>
                                        <a class="float-right"></a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Following</b>
                                        <a class="float-right"></a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Blocks</b>
                                        <a class="float-right"></a>
                                    </li>
                                </ul>
                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="button" class="btn btn-block btn-warning" data-toggle="modal" data-target="#suspendMmodal">
                                            <i class="fas fa-minus-circle"></i>
                                            <strong>Suspend</strong>
                                        </button>
                                    </div>
                                    <div class="col-md-6">
                                        <button type="button" class="btn btn-block btn-danger" data-toggle="modal" data-target="#banModal">
                                            <i class="fas fa-times-circle"></i>
                                            <strong>Ban</strong>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="card">
                            <div class="card-header p-2">
                                <ul class="nav nav-pills">
                                    <li class="nav-items">
                                        <a href="#user-info" class="nav-link active" data-toggle="tab">User Information</a>
                                    </li>
                                    <li class="nav-items">
                                        <a href="#medias" class="nav-link" data-toggle="tab">Medias</a>
                                    </li>
                                    <li class="nav-items">
                                        <a href="#upload" class="nav-link" data-toggle="tab">Upload</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="tab-pane active" id="user-info">
                                        <form action="" class="form-horizontal" method="POST">
                                            @csrf()
                                            <div class="form-group row">
                                                <label for="avatar" class="col-sm-2 col-form-label">Avatar</label>
                                                <div class="col-sm-10">
                                                    <input type="file" class="form-control-file" id="avatar" name="avatar">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="username" class="col-sm-2 col-form-label">Username</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="username" name="username" value="">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="email" class="col-sm-2 col-form-label">Email</label>
                                                <div class="col-sm-10">
                                                    <input type="email" class="form-control" id="email" name="email" value="">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="email_verified" class="col-sm-2 col-form-label">Email Verified</label>
                                                <div class="col-sm-10">
                                                    <div class="custom-control custom-switch mt-2">
                                                        <input type="checkbox" class="custom-control-input" id="email_verified">
                                                        <label class="custom-control-label" for="email_verified"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="bio" class="col-sm-2 col-form-label">Bio</label>
                                                <div class="col-sm-10">
                                                    <textarea name="bio" id="" cols="30" rows="3" class="form-control"></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="feature_tips" class="col-sm-2 col-form-label">Feature Tips</label>
                                                <div class="col-sm-10">
                                                    <div class="custom-control custom-switch mt-2">
                                                        <input type="checkbox" class="custom-control-input" id="feature_tips">
                                                        <label class="custom-control-label" for="feature_tips"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="feature_demo" class="col-sm-2 col-form-label">Feature Demo User</label>
                                                <div class="col-sm-10">
                                                    <div class="custom-control custom-switch mt-2">
                                                        <input type="checkbox" class="custom-control-input" id="feature_demo">
                                                        <label class="custom-control-label" for="feature_demo"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="gender" class="col-sm-2 col-form-label">Gender</label>
                                                <div class="col-sm-10">
                                                    <select name="gender" id="gender" class="form-control">
                                                        
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="interested_in" class="col-sm-2 col-form-label">Interested In</label>
                                                <div class="col-sm-10">
                                                    <select name="interested_in" id="interested_in" class="form-control">
                                                       
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="type" class="col-sm-2 col-form-label">Type</label>
                                                <div class="col-sm-10">
                                                    <select name="type" id="type" class="form-control">
                                                        
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="status" class="col-sm-2 col-form-label">Status</label>
                                                <div class="col-sm-10">
                                                    <select name="status" id="status" class="form-control">
                                                        
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="visibility" class="col-sm-2 col-form-label">Visibility</label>
                                                <div class="col-sm-10">
                                                    <select name="visibility" id="visibility" class="form-control">
                                                        
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="referral_id" class="col-sm-2 col-form-label">Referral User ID</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="referral_id" name="referral_id" value="">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="promo_link" class="col-sm-2 col-form-label">Promo Link</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="promo_link" name="promo_link" value="">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="password" class="col-sm-2 col-form-label">Password</label>
                                                <div class="col-sm-10">
                                                    <input type="password" class="form-control" id="password" name="password">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="password" class="col-sm-2 col-form-label">Password</label>
                                                <div class="col-sm-10 mt-2">
                                                    <p></p>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="password" class="col-sm-2 col-form-label">Password</label>
                                                <div class="col-sm-10 mt-2">
                                                    <p></p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 mt-3">
                                                    <button type="submit" class="btn btn-md btn-primary col-md-1 float-right">Submit</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="tab-pane" id="medias">
                                        medias
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
    </div>
@endsection

