<?php

namespace Aparlay\Core\Api\V1\Services;

use Aparlay\Core\Api\V1\Models\Alert;
use Aparlay\Core\Api\V1\Repositories\AlertRepository;
use Aparlay\Core\Api\V1\Traits\HasUserTrait;
use Aparlay\Core\Models\Enums\AlertStatus;
use MongoDB\BSON\ObjectId;

class AlertService extends AbstractService
{
    use HasUserTrait;

    /**
     * Responsible to create Alert for given user.
     *
     * @param  Alert $alert
     * @return Alert
     */
    public function visited(Alert $alert): Alert
    {
        $alert = Alert::find($alert->_id);
        $alert->update(['status' => AlertStatus::VISITED->value]);

        return $alert;
    }
}
