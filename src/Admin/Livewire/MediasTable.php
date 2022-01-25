<?php

namespace Aparlay\Core\Admin\Livewire;

use Aparlay\Core\Admin\Filters\FilterExact;
use Aparlay\Core\Admin\Filters\FilterPartial;
use Aparlay\Core\Admin\Filters\FilterScope;
use App\Models\Media;
use Jenssegers\Mongodb\Eloquent\Builder;
use function view;

class MediasTable extends BaseIndexComponent
{
    public $model = Media::class;

    public $selectedUser;

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
            new FilterScope('username', 'string', 'username'),
            new FilterScope('text_search', 'string', 'username'),
            new FilterExact('status', 'int'),
            new FilterExact('like_count', 'int'),
            new FilterExact('visit_count', 'int'),
            new FilterExact('sort_score', 'int'),

        ];
    }

    public function buildQuery(): Builder
    {
        $query = parent::buildQuery();

        $query->orderBy($this->sortField, $this->sortDirection);
        $query->options(['allowDiskUse' => true]);

        return $query;
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
            $this->sortClass = $this->sortDirection === 'asc' ? 'fas fa-angle-down' : 'fas fa-angle-up';
        } else {
            $this->sortDirection = 'asc';
            $this->sortClass = 'fas fa-angle-down';
        }

        $this->sortField = $field;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('default_view::livewire.medias-table', [
           'medias' => $this->index(),
        ]);
    }
}
