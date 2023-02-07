<?php

namespace Aparlay\Core\Admin\Livewire;

use Aparlay\Core\Admin\Filters\FilterDateRange;
use Aparlay\Core\Admin\Filters\FilterExact;
use Aparlay\Core\Admin\Filters\FilterPartial;
use Aparlay\Core\Admin\Models\Media;
use Aparlay\Core\Admin\Models\User;
use Jenssegers\Mongodb\Eloquent\Builder;

use function view;

class UserMedia extends BaseIndexComponent
{
    public $model = Media::class;

    protected $listeners = ['updateParent'];

    public $creatorId;

    public int $perPage = 9;

    public function updateParent()
    {
        $this->render();
    }

    public function buildQuery(): Builder
    {
        $query = parent::buildQuery();
        $query->with(['creatorObj']);
        if (! empty($this->creatorId)) {
            $query->creator($this->creatorId);
        }

        return $query;
    }

    public function getAllowedSorts(): array
    {
        return [];
    }

    protected function getFilters(): array
    {
        return [];
    }

    public function getDefaultSort(): array
    {
        return ['created_at', 'DESC'];
    }

    public function render()
    {
        return view('default_view::admin.pages.user.tabs.media-categories.user-media', [
            'medias' => $this->index(),
            'user' => User::find($this->creatorId),
        ]);
    }
}
