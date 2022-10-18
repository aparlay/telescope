<div class="form-inline col-md-8 row no-gutters">
    <button type="button" class="pb-1 col-6 mx-auto col-md-1 btn btn-sm btn-default mt-2 mt-md-0" data-interval="all">
        {{__('All')}}
    </button>
    <div class="row col-12 col-md-8 mx-auto">
        <div class="py-1 py-md-0 btn-group col-sm-6 col-md-4">
            <button type="button" class="btn btn-sm btn-default" data-interval="days" data-interval-add="-1">
                <i class="fa fa-angle-double-left"></i>
            </button>
            <button type="button" class="btn btn-sm btn-default" data-interval="days" data-interval-add="0">
                {{__('Today')}}
            </button>
            <button type="button" class="btn btn-sm btn-default" data-interval="days" data-interval-add="1">
                <i class="fa fa-angle-double-right"></i>
            </button>
        </div>
        <div class="py-1 py-md-0 btn-group col-sm-6 col-md-4">
            <button type="button" class="btn btn-sm btn-default" data-interval="weeks" data-interval-add="-1">
                <i class="fa fa-angle-double-left"></i>
            </button>
            <button type="button" class="btn btn-sm btn-default" data-interval="weeks" data-interval-add="0">
                {{__('This Week')}}
            </button>
            <button type="button" class="btn btn-sm btn-default" data-interval="weeks" data-interval-add="1">
                <i class="fa fa-angle-double-right"></i>
            </button>
        </div>
        <div class="py-1 py-md-0 btn-group col-sm-6 col-md-4">
            <button type="button" class="btn btn-sm btn-default" data-interval="months" data-interval-add="-1">
                <i class="fa fa-angle-double-left"></i>
            </button>
            <button type="button" class="btn btn-sm btn-default p-1" data-interval="months" data-interval-add="0">
                {{__('This Month')}}
            </button>
            <button type="button" class="btn btn-sm btn-default" data-interval="months" data-interval-add="1">
                <i class="fa fa-angle-double-right"></i>
            </button>
        </div>
    </div>
    <input type="text" class="pt-1 col-sm-12 col-md-3 text-center form-control-sm border-secondary" id="date-range"/>
    @push('css')
        <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/daterangepicker/daterangepicker.css') }}">
    @endpush
    @push('js')
        <script src="{{ asset('vendor/adminlte/plugins/moment/moment.min.js') }}"></script>
        <script src="{{ asset('vendor/adminlte/plugins/daterangepicker/daterangepicker.js') }}"></script>

        <script>
            window.addEventListener('livewire:load', (e) => {
                let datePicker = $('#date-range');

                const boot = () => {
                    datePicker.daterangepicker({
                        opens: 'left'
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
