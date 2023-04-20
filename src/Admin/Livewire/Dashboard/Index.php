<?php

namespace Aparlay\Core\Admin\Livewire\Dashboard;

use Aparlay\Core\Admin\Livewire\Traits\HasDateRangeFilter;
use Livewire\Component;

final class Index extends Component
{
    use HasDateRangeFilter;
    private const LAYOUTS_WITH_DATE_FILTER = [
        BaseDashboardComponent::LAYOUT_SIMPLE,
        BaseDashboardComponent::LAYOUT_ADVANCED,
    ];
    public string $layout                  = BaseDashboardComponent::LAYOUT_SIMPLE;
    public bool $showsDateFilter           = true;
    protected $listeners                   = [
        'dateInterval-changed' => 'dateIntervalChanged',
        'showAllDates-changed' => 'showAllDatesChanged',
    ];

    public function mount()
    {
        $this->showAllDates = false;
    }

    public function updated($field, $value)
    {
        if ($field == 'layout') {
            $this->emit('layout-changed', $value);
            $this->dispatchBrowserEvent('layout-changed');

            $this->showsDateFilter = in_array($value, self::LAYOUTS_WITH_DATE_FILTER, true);
        }
    }

    public function render()
    {
        return view('default_view::livewire.dashboard.index');
    }
}
