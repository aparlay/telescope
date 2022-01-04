<div id="{{ 'approve_modal_' . $document->id }}" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <form action="{{ route('core.admin.user.document.edit', ['documentId' => $document->_id])  }}" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" value="{{ \Aparlay\Core\Models\Enums\UserDocumentStatus::CONFIRMED->value }}" name="status">
                <div class="modal-header bg-success">
                    <h5 class="modal-title" id="exampleModalLiveLabel">Confirm Document</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Mark this user document as approved?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                    <button type="submit" class="btn btn-success">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>
