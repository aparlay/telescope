<div class="tab-pane" id="payment">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <h4>Notes</h4>
                <div class="col-12 table-responsive">
                    <livewire:notes-table :userId="(string)$user->_id" :headerText="'Notes'"/>
                </div>
            </div>
        </div>
    </div>
</div>
