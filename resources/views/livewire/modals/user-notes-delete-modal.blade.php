<div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Delete Note</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true close-btn">×</span>
            </button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this note?</p>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary close-btn" data-dismiss="modal">Close</button>

            <button type="button" wire:click="delete()" class="btn btn-primary close-modal">Delete</button>
        </div>
    </div>
</div>
