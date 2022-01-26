<div class="w-100">
    <button
        class="btn btn-block btn-success"
        type="button"
        wire:key="verify_button_{{ $user->_id }}}"
        wire:click="$emit('showModal', 'modals.user-verification-modal', '{{ $user->_id }}')"
    >
        <i class="fa fa-user-cog"></i> User Verification Moderation
    </button>
</div>
