<div class="form-inline">
    <div class="col-6 col-xl-1 pb-2 pb-xl-0">
        <button type="button"
                @class(['w-100', 'btn', 'btn-secondary' => $showAllDates, 'btn-default' => !$showAllDates])
                data-interval="all"
                wire:click="$emit('showAllDates-changed')"
        >
            {{__('All')}}
        </button>
    </div>
    <div class="btn-group col-6 col-xl-2 pb-2 pb-xl-0">
        <button type="button" class="btn btn-default" data-interval="days" data-interval-add="-1">
            <i class="fa fa-angle-double-left"></i>
        </button>
        <button type="button"
                class="btn @if($selected == 'today') btn-secondary @else btn-default @endif"
                data-interval="days" data-interval-add="0">
            {{__('Today')}}
        </button>
        <button type="button" class="btn btn-default" data-interval="days" data-interval-add="1">
            <i class="fa fa-angle-double-right"></i>
        </button>
    </div>
    <div class="btn-group col-6 col-xl-3 pb-2 pb-xl-0">
        <button type="button" class="btn btn-default" data-interval="weeks" data-interval-add="-1">
            <i class="fa fa-angle-double-left"></i>
        </button>
        <button type="button" class="btn @if($selected == 'this-week') btn-secondary @else btn-default @endif"
                data-interval="weeks" data-interval-add="0">
            {{__('This Week')}}
        </button>
        <button type="button" class="btn btn-default" data-interval="weeks" data-interval-add="1">
            <i class="fa fa-angle-double-right"></i>
        </button>
    </div>
    <div class="btn-group col-6 col-xl-3 pb-2 pb-xl-0">
        <button type="button" class="btn btn-default" data-interval="months" data-interval-add="-1">
            <i class="fa fa-angle-double-left"></i>
        </button>
        <button type="button" class="btn
                @if($selected == 'this-month') btn-secondary @else btn-default @endif"
                data-interval="months" data-interval-add="0">
            {{__('This Month')}}
        </button>
        <button type="button" class="btn btn-default" data-interval="months" data-interval-add="1">
            <i class="fa fa-angle-double-right"></i>
        </button>
    </div>
    <div class="col-12 col-xl-3 text-center text-left">
        <div class="d-flex bd-highlight">
            <div class="flex-grow-1">
                <input type="text" class="form-control w-100 border-secondary text-center col-12" id="date-range"/>
            </div>

            @if($exportable)
                <div class="pl-2">
                    <button wire:click="$emit('export-excel')" type="button" class="btn btn-default">
                        .csv
                    </button>
                </div>
            @endif
        </div>
    </div>

    @push('css')
        <link rel="stylesheet" href="{{ asset('vendor/daterangepicker/daterangepicker.css') }}">
    @endpush
    @push('js')
        <script src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script src="{{ asset('vendor/daterangepicker/daterangepicker.js') }}"></script>

        <script>
            window.addEventListener('livewire:load', (e) => {
                let datePicker = $('#date-range');

                const boot = () => {
                    datePicker.daterangepicker({
                        opens: 'left',
                        locale: {
                            format: 'DD/MM/YYYY'
                        }
                    }, function (start, end, label) {
                        setDateRangePicker(start, end, false);
                    });
                }

                boot();

                let changes = {
                    days: 0,
                    weeks: 0,
                    months: 0
                };

                const intervals = {
                    days: 'day',
                    weeks: 'isoweek',
                    months: 'month'
                }

                $("[data-interval]").click(function () {
                    let interval = $(this).data('interval');

                    if (interval === 'all') {
                        setDateRangePicker(
                            moment().subtract(5, 'years'),
                            moment().add(5, 'years')
                        )

                        return;
                    }

                    let change = $(this).data('interval-add');

                    if (change === 0) {
                        changes = {
                            days: 0,
                            weeks: 0,
                            months: 0
                        };
                    } else {
                        changes[interval] += change;
                    }

                    let time = moment();

                    for (let intervalName in changes) {
                        let intervalChange = changes[intervalName];

                        time.add(intervalChange, intervalName);
                    }

                    setDateRangePicker(
                        time.clone().utc().startOf(intervals[interval]),
                        time.clone().utc().endOf(intervals[interval])
                    );
                });

                function setDateRangePicker(startDate, endDate, setDatePicker = true) {
                    if (setDatePicker) {
                        datePicker.data('daterangepicker').setStartDate(startDate);
                        datePicker.data('daterangepicker').setEndDate(endDate);
                    }

                    @this.set('dateInterval', [
                        startDate.format('YYYY-MM-DD'),
                        endDate.format('YYYY-MM-DD')
                    ]);
                }

            });

            window.addEventListener('layout-changed', (event) => {
                window.dispatchEvent(new Event('livewire:load'));
            });
        </script>
    @endpush
</div>
