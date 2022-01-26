<?php

namespace Aparlay\Core\Admin\Filters;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use MongoDB\BSON\UTCDateTime;

class FilterDateRange extends AbstractBaseFilter
{
    public function __construct(
        protected string $fieldName,
        protected string $fieldType,
        protected array $rangeNames = ['start', 'end']
    ) {
    }

    public function __invoke($query)
    {
        $rangeNameStart = Arr::get($this->rangeNames, 0);
        $rangeNameEnd = Arr::get($this->rangeNames, 1);

        $startDate = Arr::get($this->fieldValue, $rangeNameStart, false);
        $endDate = Arr::get($this->fieldValue, $rangeNameEnd, false);

        $isDateStartValid = Carbon::canBeCreatedFromFormat($startDate, 'Y-m-d');
        $isDateEndValid = Carbon::canBeCreatedFromFormat($endDate, 'Y-m-d');

        if ($isDateStartValid) {
            $start = new UTCDateTime(Carbon::createFromFormat('Y-m-d', $startDate)->endOfDay());
            $query->where('created_at', '>=', $start);
        }

        if ($isDateEndValid) {
            $start = new UTCDateTime(Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay());
            $query->where('created_at', '<=', $start);
        }
    }
}