<div class="container bg-white">
    @if($showsDateFilter)
        <div class="py-1 row">
            <div class="col-3">
                <button type="button" class="btn btn-sm btn-default">
                    .xls
                </button>
            </div>
            <div class="col-12">
                <livewire:components.date-range-picker/>
            </div>
        </div>
        <hr>
    @endif
    <div class="py-1 row">
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
    <livewire:dashboard.stats/>
    <livewire:dashboard.funnel/>
    <livewire:dashboard.credit-rate-changes/>
    {{--            <livewire:admin.dashboard.top-credit-balance/>--}}
    {{--            <livewire:admin.dashboard.table/>--}}
</div>
