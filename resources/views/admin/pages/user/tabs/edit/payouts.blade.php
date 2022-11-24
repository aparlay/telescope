@php
    use Illuminate\Support\Arr;
    use Aparlay\Core\Models\Enums\UserVerificationStatus;
@endphp

<form action="{{ route('core.admin.user.update.payouts', ['user' => $user->_id]) }}" class="form-horizontal" method="post" id="profile-form">
    @csrf()
    @method('PUT')
    <div class="card collapsed-card user-profile-card" id="user-payouts">
        <div class="card-header">
            <h3 class="card-title">Payouts</h3>
            <div class="card-tools">
                <button type="button" class="btn text-blue card-edit" data-edit="user-payouts">Edit <i class="fas fa-pen"></i></button>
                <button type="submit" class="btn text-blue card-save d-none">Save <i class="fas fa-save"></i></button>
                <button
                    type="button"
                    class="btn btn-tool"
                    data-card-widget="collapse"
                    data-expand-icon="fa-chevron-down"
                    data-collapse-icon="fa-chevron-up"
                ><i class="fas fa-chevron-up"></i></button>
            </div>
        </div>
        <div class="card-body">
            <div class="tab-pane active" id="user-info">
                <div class="form-group row">
                    <label for="bank_transfer" class="col-sm-2 col-form-label">Bank Transfer</label>
                    <div class="col-sm-10">
                        <div class="mt-2 pl-4">
                            <p>--</p>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="crypto_transfer" class="col-sm-2 col-form-label">Crypto Transfer</label>
                    <div class="col-sm-10">
                        <div class="mt-2 pl-4">
                            <p>--</p>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="bank_transfer_select_payer" class="col-sm-2 col-form-label">Bank Transfer Select Payer</label>
                    <div class="col-sm-10">
                        <div class="mt-2 pl-4">
                            <p>--</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
