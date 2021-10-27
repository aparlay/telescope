@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <p>{{$error}}</p>
        @endforeach
    </div>
@endif
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
