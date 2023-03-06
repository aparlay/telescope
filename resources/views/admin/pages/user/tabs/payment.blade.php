<div class="tab-pane" id="payment">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 table-responsive">
                    <livewire:tips-table :creatorId="(string)$user->_id" :headerText="'Sent Tips'"/>
                </div>

                <div class="col-12 table-responsive">
                    <livewire:tips-table :userId="(string)$user->_id" :headerText="'Earning Tips'"/>
                </div>

                <div class="col-12 table-responsive">
                    <livewire:subscriptions-table :creatorId="(string)$user->_id" :headerText="'Subscriptions'"/>
                </div>

                <div class="col-12 table-responsive">
                    <livewire:subscriptions-table :userId="(string)$user->_id" :headerText="'Subscribers'"/>
                </div>
            </div>
        </div>
    </div>
</div>
