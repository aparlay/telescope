<?php

namespace Aparlay\Core\Api\V1\Services;

use Aparlay\Core\Api\V1\Models\Alert;
use Aparlay\Core\Api\V1\Repositories\AlertRepository;
use Aparlay\Core\Api\V1\Traits\HasUserTrait;
use Aparlay\Core\Models\Enums\AlertStatus;

class AlertService extends AbstractService
{
    use HasUserTrait;
    protected AlertRepository $AlertRepository;

    public function __construct()
    {
        $this->AlertRepository = new AlertRepository(new Alert());
    }

    /**
     * Responsible to create Alert for given user.
     *
     * @param  Alert $alert
     * @return Alert
     */
    public function visited(Alert $alert): Alert
    {
        return $this->AlertRepository->update(['status' => AlertStatus::VISITED->value], $alert->_id);
    }
}
