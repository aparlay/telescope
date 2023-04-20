<?php

namespace Aparlay\Core\Admin\Livewire;

use Aparlay\Core\Admin\Filters\FilterDateRange;
use Aparlay\Core\Admin\Filters\FilterExact;
use Aparlay\Core\Admin\Filters\FilterPartial;
use Aparlay\Core\Admin\Models\MediaComment;
use Jenssegers\Mongodb\Eloquent\Builder;
use MongoDB\BSON\ObjectId;

class MediaCommentsTable extends BaseIndexComponent
{
    public $model               = MediaComment::class;
    public $headerText          = 'Media Comment';
    public $showOnlyForApproval = false;
    protected $listeners        = ['updateParent'];
    public $mediaId;
    public $userId;

    public function updateParent()
    {
        $this->render();
    }

    public function getAllowedSorts()
    {
        return [
            'creator_username',
            'created_at',
            'status',
        ];
    }

    public function getDefaultSort(): array
    {
        return ['updated_at', 'DESC'];
    }

    /**
     * @return array
     */
    protected function getFilters()
    {
        return [
            new FilterPartial('creator_username', 'string', 'creator.username'),
            new FilterExact('status', 'int'),
            new FilterDateRange('created_at', 'array', ['start', 'end']),
        ];
    }

    public function buildQuery(): Builder|\Illuminate\Contracts\Database\Query\Builder
    {
        $query = parent::buildQuery()->with(['creatorObj']);

        if (!empty($this->userId)) {
            $query->where('creator._id', new ObjectId($this->userId));
        }

        if (!empty($this->mediaId)) {
            $query->where('media_id', new ObjectId($this->mediaId));
        }

        return $query;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('default_view::livewire.media-comments-table', [
            'models' => $this->index(),
            'hiddenFields' => [
                'creator_username' => !empty($this->userId),
                'status' => $this->showOnlyForApproval,
            ],
        ]);
    }
}
