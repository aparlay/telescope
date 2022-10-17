<?php

namespace Aparlay\Core\Admin\Livewire\Traits;

use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime;

trait HasDateRangeFilter
{
    public array $dateInterval = [];

    public function dateIntervalChanged($dateInterval): void
    {
        $this->dateInterval = $dateInterval;

        $this->emit('updateParent');
    }

    /**
     * @return UTCDateTime[]
     */
    private function getUtcDateInterval(): array
    {
        if (empty($this->dateInterval)) {
            return [];
        } else {
            return [
                new UTCDateTime($this->startDate()->startOfDay()),
                new UTCDateTime($this->endDate()->endOfDay()),
            ];
        }
    }

    protected function startDate(): Carbon
    {
        return Carbon::createFromFormat('Y-m-d', $this->dateInterval[0]);
    }

    protected function endDate(): Carbon
    {
        return Carbon::createFromFormat('Y-m-d', $this->dateInterval[1]);
    }
}
