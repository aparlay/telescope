<div class="tab-pane" id="payment">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                 <h4>Notes</h4>
                </div>
             
                <div class="col-md-6">
                    <button type="submit" class="btn btn-md btn-success col-md-3 float-right" class="btn btn-block btn-warning" data-toggle="modal" data-target="#noteModal" data-form-type="action"><i class="fas fa-plus fa-xs"></i> Add note</button>
                </div>
               
              
                <div class="col-12 table-responsive">
                    <livewire:notes-table :userId="(string)$user->_id">
                </div>
            </div>
        </div>
    </div>
</div>
