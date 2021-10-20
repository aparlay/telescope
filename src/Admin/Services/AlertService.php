<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Core\Admin\Models\Alert;
use Aparlay\Core\Admin\Repositories\AlertRepository;

class AlertService
{
    protected AlertRepository $alertRepository;

    public function __construct()
    {
        $this->alertRepository = new AlertRepository(new Alert());
    }

    /**
     * Finds the Alert model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $_id
     * @return User the loaded model
     */
    public function findUserModel($user_id)
    {
        return $this->alertRepository->findUserModel($user_id);
    }

    /**
     * Finds the Alert model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $_id
     * @return Media the loaded model
     */
    public function findMediaModel($media_id)
    {
        return $this->alertRepository->findMediaModel($media_id);
    }
}
