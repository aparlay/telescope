<div class="tab-pane table-responsive" id="email">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 table-responsive">
                    <livewire:payouts-table :showOnlyForApproval="false" :userId="(string)$user->_id" :headerText="'Payouts'"/>
                </div>
            </div>
        </div>
    </div>
</div>
