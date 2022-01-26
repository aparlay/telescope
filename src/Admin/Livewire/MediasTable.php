<?php

namespace Aparlay\Core\Admin\Livewire;

use Aparlay\Core\Admin\Filters\FilterExact;
use Aparlay\Core\Admin\Filters\FilterPartial;
use Aparlay\Core\Admin\Filters\FilterScope;
use Aparlay\Core\Admin\Filters\FilterDateRange;
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
            new FilterPartial('username', 'string', 'username'),
            new FilterScope('text_search', 'string', 'username'),
            new FilterExact('status', 'int'),
            new FilterExact('like_count', 'int'),
            new FilterExact('visit_count', 'int'),
            new FilterExact('sort_score', 'int'),
            new FilterDateRange('created_at', 'array', ['start', 'end']),

        ];
    }

    public function getAllowedSorts()
    {
        return [
            'file',
            'created_by',
            'description',
            'status',
            'like_count',
            'visit_count',
            'sort_score',
            'created_at',
        ];
    }

    public function buildQuery(): Builder
    {
        $query = parent::buildQuery();

        return $query;
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
