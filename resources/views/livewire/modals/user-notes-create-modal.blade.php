<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Add a new note</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
            <div class="form-group">
                <label for="">Message</label>
                <textarea class="form-control" wire:model="message"></textarea>

                @error('message')
                    <div class="text text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary close-btn" data-bs-dismiss="modal">Close</button>

            <button type="button" wire:click="create()" class="btn btn-primary close-modal">Create</button>
        </div>
    </div>
</div>

