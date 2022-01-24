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
            new FilterPartial('username', 'string'),
            new FilterExact('gender', 'int'),
            new FilterScope('email', 'string', 'email'),
            new FilterScope('country', 'string', 'countryAlpha2'),
            new FilterExact('status', 'int'),
            new FilterScope('text_search', 'string', 'textSearch'),
            new FilterExact('verification_status', 'int'),
        ];
    }

    public function buildQuery(): Builder
    {
        $query = parent::buildQuery();

        $query->orderByDesc('created_at');
        $query->options(['allowDiskUse' => true]);

        return $query;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        dd($this->index());

        return view('default_view::livewire.medias-table', [
           'medias' => $this->index(),
        ]);
    }
}
