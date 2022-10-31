<?php

namespace Aparlay\Core\Admin\Livewire\Traits;

use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime;

trait HasDateRangeFilter
{
    public array $dateInterval = [];

    public bool $showAllDates = true;

    public function showAllDatesChanged(): void
    {
        $this->showAllDates = (!$this->showAllDates);
    }

    public function dateIntervalChanged($dateInterval): void
    {
        $this->dateInterval = $dateInterval;

        if (! empty($this->dateInterval)) {
            $this->showAllDates = false;
        }
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
