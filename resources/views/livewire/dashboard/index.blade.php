<div class="container bg-white">
    @if($showsDateFilter)
        <div class="py-1 row">
            <div class="col-1 my-auto">
                <button type="button" class="btn btn-sm btn-default">
                    .xls
                </button>
            </div>
            <div class="col-11">
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
    <div>
        <livewire:dashboard.stats/>
    </div>
{{--    <hr>--}}
    {{--    <div>--}}
    {{--        <livewire:dashboard.funnel/>--}}
    {{--    </div>--}}
    {{--    <hr>--}}
    {{--    <div>--}}
    {{--        <livewire:dashboard.top-credit-balance/>--}}
    {{--    </div>--}}
    {{--            <livewire:admin.dashboard.table/>--}}
</div>
