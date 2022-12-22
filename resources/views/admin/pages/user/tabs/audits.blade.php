<div class="tab-pane" id="audits">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 table-responsive">
                    <livewire:audits-table :auditableType="'User'" :auditableId="(string) $user->_id"/>
                </div>
            </div>
        </div>
    </div>
</div>
