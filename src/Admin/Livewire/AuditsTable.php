<?php

namespace Aparlay\Core\Admin\Livewire;

use Aparlay\Core\Admin\Filters\FilterDateRange;
use Aparlay\Core\Admin\Filters\FilterExact;
use Aparlay\Core\Admin\Filters\FilterPartial;
use Aparlay\Core\Models\Audit;
use Jenssegers\Mongodb\Eloquent\Builder;
use MongoDB\BSON\ObjectId;

class AuditsTable extends BaseIndexComponent
{
    public $model = Audit::class;
    public $auditableId;
    public $auditableType;

    public function getDefaultSort(): array
    {
        return ['created_at', 'DESC'];
    }

    public function getAllowedSorts()
    {
        return [
            'user',
            'created_at',
        ];
    }

    /**
     * @return array
     */
    protected function getFilters()
    {
        return [
            new FilterPartial('user_username', 'string', 'creator.username'),
            new FilterExact('auditable_type', 'string'),
            new FilterPartial('auditable_id', 'string'),
            new FilterDateRange('created_at', 'array', ['start', 'end']),
        ];
    }

    public function buildQuery(): Builder
    {
        $query = parent::buildQuery()->with(['user']);

        if (!empty($this->auditableType)) {
            $query->where('auditable_type', $this->auditableType);
        }
        if (!empty($this->auditableId)) {
            $query->where('auditable_id', new ObjectId($this->auditableId));
        }

        return $query;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('default_view::livewire.audits-table', [
            'audits' => $this->index(),
            'hiddenFields' => [],
        ]);
    }
}
