<div id="rejectModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <form action="{{ route('core.admin.user.document.edit', ['documentId' => 0])  }}" method="POST" name="rejectForm">
                @csrf
                @method('PATCH')
                <input type="hidden" value="{{ \Aparlay\Core\Models\Enums\UserDocumentStatus::REJECTED->value }}" name="status">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title" id="exampleModalLiveLabel">Reject Document</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Mark this user document as rejected?</p>

                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Reject Reason:</label>
                        <input type="text" class="form-control" value="" name="reject_reason">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>
