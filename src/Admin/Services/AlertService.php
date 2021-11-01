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

    public function create($request)
    {
        $this->mediaRepository->find($request->media_id);
        $this->userRepository->find($request->user_id);

        return $this->alertRepository->store($request);
    }
}
