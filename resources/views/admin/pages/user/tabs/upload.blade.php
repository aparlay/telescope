<div class="tab-pane" id="upload">
    <form method="post" action="{{ route('core.admin.user.media.save-upload') }}" >
        <div class="flow-drop" data-upload-url="{{ route('core.admin.user.media.upload') }}" ondragenter="jQuery(this).addClass('flow-dragover');" ondragend="jQuery(this).removeClass('flow-dragover');" ondrop="jQuery(this).removeClass('flow-dragover');">
            Drop files here to upload
            <span>or</span>
            <a class="btn btn-md btn-outline-primary upload-btn flow-browse mt-3">
                select from your computer
            </a>
        </div>
        @csrf
        <div class="flow-list col-md-12 mt-3"></div>

        <input type="hidden" id="media_file" name="file">
        <input type="hidden" name="user_id" value="{{ $user->_id }}" >
        <div class="row mt-3">
            <div class="col-12">
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" class="form-control" id="description" cols="30" rows="3"></textarea>
                </div>
            </div>
        </div>
        <div class="row">

            <div class="col-4">
                <button class="btn btn-block btn-primary upload-video-button" name="create-button"><i class="fa fa-upload"></i><strong>Upload</strong></button>
            </div>
        </div>
    </form>
</div>
