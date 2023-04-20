<?php

namespace Aparlay\Core\Admin\Livewire;

use Aparlay\Core\Admin\Filters\FilterDateRange;
use Aparlay\Core\Admin\Filters\FilterExact;
use Aparlay\Core\Admin\Filters\FilterPartial;
use Aparlay\Core\Models\Enums\MediaStatus;
use App\Models\Media;
use Jenssegers\Mongodb\Eloquent\Builder;

use function view;

class MediasModerationTable extends BaseIndexComponent
{
    public $model        = Media::class;
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
        $query = parent::buildQuery();
        $query->whereIn('status', [MediaStatus::COMPLETED->value, MediaStatus::IN_REVIEW->value])->with(['creatorObj']);

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
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('default_view::livewire.medias-moderation-table', [
            'medias' => $this->index(),
        ]);
    }
}
