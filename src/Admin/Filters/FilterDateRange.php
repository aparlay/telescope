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

        if ($startDate && $endDate) {
            $isValidaDateStart = Carbon::canBeCreatedFromFormat($startDate, 'Y-m-d');
            $isValidaDateEnd = Carbon::canBeCreatedFromFormat($endDate, 'Y-m-d');

            if ($isValidaDateStart && $isValidaDateEnd) {
                $start = new UTCDateTime(Carbon::createFromFormat('Y-m-d', $startDate)->endOfDay());
                $end = new UTCDateTime(Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay());

                $query->whereBetween('created_at', [$start, $end]);
            }
        } elseif ($startDate) {
            $isValidaDateStart = Carbon::canBeCreatedFromFormat($startDate, 'Y-m-d');

            if ($isValidaDateStart) {
                $start = new UTCDateTime(Carbon::createFromFormat('Y-m-d', $startDate)->endOfDay());

                $query->where('created_at', '>=', $start);
            }
        } elseif ($endDate) {
            $isValidaDateEnd = Carbon::canBeCreatedFromFormat($endDate, 'Y-m-d');

            if ($isValidaDateEnd) {
                $end = new UTCDateTime(Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay());

                $query->where('created_at', '<=', $end);
            }
        }
    }
}
