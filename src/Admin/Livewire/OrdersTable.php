<?php

namespace Aparlay\Core\Admin\Livewire;

use Aparlay\Core\Admin\Filters\FilterDateRange;
use Aparlay\Core\Admin\Filters\FilterExact;
use Aparlay\Core\Admin\Filters\FilterPartial;
use Aparlay\Payment\Models\Order;
use Jenssegers\Mongodb\Eloquent\Builder;
use MongoDB\BSON\ObjectId;

class OrdersTable extends BaseIndexComponent
{
    public $model = Order::class;

    protected $listeners = ['updateParent'];

    private $userId;

    public function updateParent()
    {
        $this->render();
    }

    public function getAllowedSorts()
    {
        return [
            'entity',
            'currency',
            'amount',
            'created_at',
            'status',
            'creator.username',
        ];
    }

    /**
     * @return array
     */
    protected function getFilters()
    {
        return [
            new FilterPartial('creator_username', 'string', 'creator.username'),
            new FilterExact('entity', 'int'),
            new FilterExact('status', 'int'),
            new FilterDateRange('created_at', 'array', ['start', 'end']),
        ];
    }

    public function buildQuery(): Builder
    {
        $query = parent::buildQuery();

        if (! empty($this->userId)) {
            $query->where('creator._id', new ObjectId($this->userId));
        }
        return $query;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('default_view::livewire.orders-table', [
           'orders' => $this->index(),
        ]);
    }
}
