<?php

namespace Aparlay\Core\Admin\Livewire\Components;

use Livewire\Component;

final class DateRangePicker extends Component
{
    public array $dateInterval = [];

    public function render()
    {
        return view('default_view::livewire.components.date-range-picker');
    }

    public function updated($field, $value)
    {
        if ($field == 'dateInterval') {
            $this->emit('dateInterval-changed', $value);
        }
    }
}
