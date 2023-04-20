<?php

namespace Aparlay\Core\Admin\Livewire\Components;

use Livewire\Component;

final class DateRangePicker extends Component
{
    public array $dateInterval = [];
    public string $selected    = '';
    public array $today;
    public array $thisWeek;
    public array $thisMonth;
    public bool $showAllDates  = true;
    public bool $exportable    = true;
    protected $listeners       = [
        'showAllDates-changed' => 'showAllDatesChanged',
    ];

    public function mount()
    {
        $date            = today()->format('Y-m-d');
        $this->today     = [$date, $date];

        if (today()->isMonday()) {
            $this->thisWeek = [today()->format('Y-m-d'), today()->next(0)->format('Y-m-d')];
        } else {
            $this->thisWeek = [today()->previous(1)->format('Y-m-d'), today()->next(0)->format('Y-m-d')];
        }

        $this->thisMonth = [today()->firstOfMonth()->format('Y-m-d'), today()->lastOfMonth()->format('Y-m-d')];

        if (!$this->showAllDates && empty($this->dateInterval)) {
            $this->dateInterval = $this->today;
        }
    }

    public function render()
    {
        $this->getSelectedPeriod($this->dateInterval);

        return view('default_view::livewire.components.date-range-picker');
    }

    public function updated($field, $value)
    {
        if ($field == 'dateInterval') {
            $this->emit('dateInterval-changed', $value);
            if (!empty($value)) {
                $this->showAllDates = false;
            }
        }
    }

    public function showAllDatesChanged()
    {
        $this->showAllDates = !($this->showAllDates);
    }

    private function getSelectedPeriod(array $value)
    {
        if ($value == $this->today) {
            $this->selected = 'today';
        } elseif ($value == $this->thisWeek) {
            $this->selected = 'this-week';
        } elseif ($value == $this->thisMonth) {
            $this->selected = 'this-month';
        } else {
            $this->selected = '';
        }
    }
}
