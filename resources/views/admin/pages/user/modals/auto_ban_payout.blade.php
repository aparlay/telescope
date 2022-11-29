<div id="{{ $method }}-auto-ban-payout-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <form action="{{ route('core.admin.user.update.payoutsettings', ['user' => $user->_id])  }}" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" name="setting[payout][auto_ban_payout]" value="{{ 'set' === $method ? 1 : 0 }}">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="exampleModalLiveLabel">{{ ucfirst($method) }} Auto Ban Payout</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to {{ $method }} auto ban payout?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>
