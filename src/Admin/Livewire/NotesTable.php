<?php

namespace Aparlay\Core\Admin\Livewire;

use Aparlay\Core\Admin\Filters\FilterDateRange;
use Aparlay\Core\Admin\Filters\FilterExact;
use Aparlay\Core\Admin\Filters\FilterPartial;
use Aparlay\Core\Admin\Filters\FilterScope;
use Aparlay\Core\Models\Note;
use Jenssegers\Mongodb\Eloquent\Builder;

class NotesTable extends BaseIndexComponent
{
    public $model = Note::class;
    protected $listeners = ['updateParent'];

    public function updateParent()
    {
        $this->render();
    }

    public function getAllowedSorts()
    {
        return [
            'created_by',
            'created_at',
            'message',
        ];
    }

    /**
     * @return array
     */
    protected function getFilters()
    {
        return [
            new FilterPartial('created_by', 'string'),
            (new FilterExact('message', 'int')),
            new FilterDateRange('created_at', 'array', ['start', 'end']),
        ];
    }

    public function buildQuery(): Builder
    {
        $query = parent::buildQuery();

        $query->options(['allowDiskUse' => true]);

        return $query;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('default_view::livewire.notes-table', [
           'notes' => $this->index(),
        ]);
    }
}
