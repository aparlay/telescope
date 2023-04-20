<?php

namespace Aparlay\Core\Admin\Livewire;

use Aparlay\Core\Admin\Filters\FilterDateRange;
use Aparlay\Core\Admin\Filters\FilterExact;
use Aparlay\Core\Admin\Filters\FilterPartial;
use Aparlay\Core\Models\Note;
use Jenssegers\Mongodb\Eloquent\Builder;

class NotesTable extends BaseIndexComponent
{
    public $model       = Note::class;
    public $userId;
    public $category;
    public int $perPage = 10;

    public function getDefaultSort(): array
    {
        return ['created_at', 'DESC'];
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
            new FilterPartial('creator_username', 'string', 'creator.username'),
            new FilterExact('type', 'int'),
            new FilterExact('category', 'int'),
            new FilterPartial('message', 'string'),
            new FilterDateRange('created_at', 'array', ['start', 'end']),
        ];
    }

    public function buildQuery(): Builder
    {
        $query = parent::buildQuery()->with(['creatorObj'])->isNotDeleted();

        if (!empty($this->userId)) {
            $query->user($this->userId);
        }

        if (!empty($this->category)) {
            $query->category($this->category);
        }

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
