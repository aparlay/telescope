<?php

namespace Aparlay\Core\Api\V1\Services;

use Aparlay\Core\Api\V1\Models\Alert;
use Aparlay\Core\Api\V1\Traits\HasUserTrait;
use Aparlay\Core\Models\Enums\AlertStatus;

class AlertService extends AbstractService
{
    use HasUserTrait;

    /**
     * Responsible to create Alert for given user.
     */
    public function visited(Alert $alert): Alert
    {
        $alert->update(['status' => AlertStatus::VISITED->value]);

        return $alert;
    }
}
