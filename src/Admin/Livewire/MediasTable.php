<?php

namespace Aparlay\Core\Admin\Livewire;

use Aparlay\Core\Admin\Filters\FilterDateRange;
use Aparlay\Core\Admin\Filters\FilterExact;
use Aparlay\Core\Admin\Filters\FilterPartial;
use Aparlay\Core\Admin\Models\Media;
use Jenssegers\Mongodb\Eloquent\Builder;

use function view;

class MediasTable extends BaseIndexComponent
{
    public $model = Media::class;

    protected $listeners = ['updateParent'];

    public $creatorId;

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
            new FilterPartial('creator_username', 'string', 'creator.username'),
            new FilterPartial('text_search', 'string', 'creator.username'),
            new FilterExact('status', 'int'),
            new FilterExact('like_count', 'int'),
            new FilterExact('visit_count', 'int'),
            new FilterDateRange('created_at', 'array', ['start', 'end']),
        ];
    }

    public function buildQuery(): Builder
    {
        if (! empty($this->sort['sort_scores.guest'])
            && $this->sort['sort_scores.guest'] === -1
            && empty($this->filter['status'])) {
            $this->filter['status'] = 5;
        }

        $query = parent::buildQuery();
        $query->with(['creatorObj']);
        if (! empty($this->creatorId)) {
            $query->creator($this->creatorId);
        }

        return $query;
    }

    public function getDefaultSort(): array
    {
        return ['created_at', 'DESC'];
    }

    public function getAllowedSorts()
    {
        return [
            'description',
            'status',
            'like_count',
            'visit_count',
            'created_at',
            'sort_scores.guest',
            'sort_scores.returned',
            'sort_scores.registered',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('default_view::livewire.medias-table', [
            'medias' => $this->index(),
            'hiddenFields' => ['creator_username' => ! empty($this->creatorId)],
        ]);
    }
}
