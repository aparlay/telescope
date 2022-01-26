<?php

namespace Aparlay\Core\Admin\Livewire;

use Aparlay\Core\Admin\Filters\FilterDateRange;
use Aparlay\Core\Admin\Filters\FilterExact;
use Aparlay\Core\Admin\Filters\FilterPartial;
use Aparlay\Core\Admin\Filters\FilterScope;
use Aparlay\Core\Models\Enums\UserVerificationStatus;
use Aparlay\Payment\Models\CreditCard;
use App\Models\User;
use Jenssegers\Mongodb\Eloquent\Builder;
use MongoDB\BSON\ObjectId;

class CreditCardsTable extends BaseIndexComponent
{
    public $model = CreditCard::class;

    public $userId;

    protected $listeners = ['updateParent'];

    public function updateParent()
    {
        $this->render();
    }

    public function getAllowedSorts()
    {
        return [
            'holder_name',
            'status',
            'expire_year',
            'expire_month',
            'masked_card_number',
            'card_brand',
            'created_at',
        ];
    }

    /**
     * @return array
     */
    protected function getFilters()
    {
        return [
            new FilterPartial('holder_name', 'string'),
            new FilterPartial('expire_year', 'string'),
            new FilterPartial('expire_month', 'string'),
            new FilterPartial('card_brand', 'string'),
            new FilterExact('status', 'int'),
            new FilterDateRange('created_at', 'array', ['start', 'end']),
        ];
    }

    public function buildQuery(): Builder
    {
        $query = parent::buildQuery();

        if (!empty($this->userId)) {
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
        return view('default_view::livewire.credit-cards-table', [
           'creditCards' => $this->index(),
        ]);
    }
}
