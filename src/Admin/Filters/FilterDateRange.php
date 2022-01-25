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

        $startDate = Arr::get($this->fieldValue, $rangeNameStart);
        $endDate = Arr::get($this->fieldValue, $rangeNameEnd);

        if ($startDate && $endDate) {
            $isValidaDateStart = Carbon::canBeCreatedFromFormat($startDate, 'd/m/Y');
            $isValidaDateEnd = Carbon::canBeCreatedFromFormat($endDate, 'd/m/Y');

            if ($isValidaDateStart && $isValidaDateEnd) {
                $start = new UTCDateTime(Carbon::createFromFormat('d/m/Y', $startDate)->endOfDay());
                $end = new UTCDateTime(Carbon::createFromFormat('d/m/Y', $endDate)->endOfDay());

                $query->whereBetween('created_at', [$start, $end]);
            }
        }
    }
}
