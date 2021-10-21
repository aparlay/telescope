<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Core\Admin\Models\Alert;
use Aparlay\Core\Admin\Models\Media;
use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Admin\Repositories\AlertRepository;
use Aparlay\Core\Admin\Repositories\MediaRepository;
use Aparlay\Core\Admin\Repositories\UserRepository;

class AlertService
{
    protected AlertRepository $alertRepository;
    protected UserRepository $userRepository;
    protected MediaRepository $mediaRepository;

    public function __construct()
    {
        $this->alertRepository = new AlertRepository(new Alert());
        $this->userRepository = new UserRepository(new User());
        $this->mediaRepository = new MediaRepository(new Media());
    }

    /**
     * Finds the Alert model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $_id
     * @return User the loaded model
     */
    public function findUserModel($user_id)
    {
        return $this->userRepository->find($user_id);
    }

    /**
     * Finds the Alert model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $_id
     * @return Media the loaded model
     */
    public function findMediaModel($media_id)
    {
        return $this->mediaRepository->find($media_id);
    }

    public function create($request)
    {
        return $this->alertRepository->store($request);
    }
}
