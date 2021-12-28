<div class="tab-pane" id="documents">
    <div class="content">
        <div class="container-fluid">
            <div class="row">

                <h4>User Documents</h4>
                @foreach($user->userDocumentObjs as $document)

                    <span class="badge">{{ $document->status }}</span>
                    <div class="img-responsive">
                        <img src="{{$document->temporaryUrl() }}" alt="" width="200" height="200">
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
