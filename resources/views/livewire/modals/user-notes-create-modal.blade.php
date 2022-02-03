<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Create a new Note</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true close-btn">×</span>
            </button>
        </div>

        <div class="modal-body">
            <div class="form-group">
                <label for="">Message</label>
                <input class="form-control" wire:model="message">
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary close-btn" data-dismiss="modal">Close</button>

            <button type="button" wire:click="create()" class="btn btn-primary close-modal">Create</button>
        </div>
    </div>
</div>

