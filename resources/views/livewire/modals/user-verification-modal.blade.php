@php
    use Aparlay\Core\Models\Enums\UserVerificationStatus;
    use Aparlay\Core\Models\User;
@endphp

<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Verify user</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true close-btn">×</span>
            </button>
        </div>
        <div class="modal-body">
            <p>Verify user</p>

            <div>
                <label for="">Verification Status</label>
                <select class="form-control" wire:model="verification_status">
                    @foreach(User::getVerificationStatuses() as $value => $label)
                        <option value="{{$value}}">{{$label}}</option>
                    @endforeach
                </select>
            </div>

            <div wire:model="documents"></div>



        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary close-btn" data-dismiss="modal">Close</button>

            <button type="button" wire:click="save()" class="btn btn-primary close-modal">Save</button>
        </div>
    </div>
</div>
