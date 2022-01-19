@php
    use Aparlay\Core\Models\Enums\UserVerificationStatus;
@endphp

<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Approve user</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true close-btn">×</span>
            </button>
        </div>
        <div class="modal-body">
            <p>{{ $modalTitle }}</p>

            @if ($action === 'markAsRejected')
                <div class="mt-2">
                    <input class="form-control" type="text" wire:model="reject_reason" placeholder="Reject reason">
                </div>
            @endif
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary close-btn" data-dismiss="modal">Close</button>

            <button type="button" wire:click="{{ $action }}" class="btn btn-primary close-modal">Save</button>
        </div>
    </div>
</div>
