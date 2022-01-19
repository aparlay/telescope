<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Approve user</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true close-btn">×</span>
            </button>
        </div>
        <div class="modal-body">
            {{ $modalTitle }}
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary close-btn" data-dismiss="modal">Close</button>

            @if ($action === 'approve')
                <button type="button" wire:click="markAsVerified('{{ $selectedUser }}')" class="btn btn-primary close-modal">Save changes</button>
            @else
                <button type="button" wire:click="markAsRejected('{{ $selectedUser }}')" class="btn btn-primary close-modal">Reject</button>
            @endif
        </div>
    </div>
</div>
