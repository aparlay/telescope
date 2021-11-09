<div class="row">
    <div class="col-2">
        <div class="form-group">
            <label>Date range button:</label>
            <div class="input-group">
                <button type="button" class="btn btn-default float-right" id="daterange-btn">
                    <i class="far fa-calendar-alt"></i> Date range picker
                    <i class="fas fa-caret-down"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="col-4 datepicker-preview-container">
        <div class="form-group">
            <input type="text" class="form-control" data-column="{{ $column }}" id="date-preview" readonly>
        </div>
    </div>
</div>
