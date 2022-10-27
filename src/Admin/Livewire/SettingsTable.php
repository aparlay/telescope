<?php

namespace Aparlay\Core\Admin\Livewire;

use Aparlay\Core\Admin\Filters\FilterDateRange;
use Aparlay\Core\Admin\Filters\FilterExact;
use Aparlay\Core\Admin\Filters\FilterPartial;
use Aparlay\Core\Admin\Filters\FilterScope;
use Aparlay\Core\Models\Setting;
use Jenssegers\Mongodb\Eloquent\Builder;

class SettingsTable extends BaseIndexComponent
{
    public $model = Setting::class;

    protected $listeners = ['updateParent'];

    public function updateParent()
    {
        $this->render();
    }

    /**
     * @return array
     */
    protected function getFilters()
    {
        return [
            new FilterPartial('text_search', 'string', 'group'),
            new FilterPartial('group', 'string'),
            new FilterPartial('title', 'string'),
            new FilterDateRange('created_at', 'array', ['start', 'end']),

        ];
    }

    public function getDefaultSort(): array
    {
        return ['created_at', 'DESC'];
    }

    public function getAllowedSorts()
    {
        return [
            'group',
            'title',
            'created_at',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('default_view::livewire.settings-table', [
           'settings' => $this->index(),
        ]);
    }
}
