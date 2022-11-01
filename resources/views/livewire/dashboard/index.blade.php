<div class="container bg-white border pt-3">
    <div class="py-2 row">
        <div class="col-12 col-md-1 py-1 py-md-0 text-center text-left">
            <button type="button" class="btn btn-sm btn-default">
                .xls
            </button>
        </div>
        <div class="col-12 col-md-11">
            <livewire:components.date-range-picker :showAllDates="$showAllDates" :dateInterval="$dateInterval"/>
        </div>
    </div>
    <hr>
    <div class="py-1 row d-none">
        <div class="px-4 col-md-3">
            <select name="layout"
                    class="form-select"
                    wire:model.lazy="layout">
                <option value="simple">Simple</option>
                <option value="advanced">Advanced</option>
                <option value="message-stats">Message Stats</option>
                <option value="funnel">Funnel</option>
                <option value="table">Table</option>
            </select>
        </div>
    </div>
    <div>
        <livewire:dashboard.stats :showAllDates="$showAllDates" :dateInterval="$dateInterval"/>
    </div>
</div>
