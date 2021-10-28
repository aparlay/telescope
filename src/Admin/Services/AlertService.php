<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Core\Admin\Models\Alert;
use Aparlay\Core\Admin\Models\Media;
use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Admin\Repositories\AlertRepository;
use Aparlay\Core\Admin\Repositories\MediaRepository;
use Aparlay\Core\Admin\Repositories\UserRepository;
use MongoDB\BSON\ObjectId;

class AlertService
{
    protected AlertRepository $alertRepository;
    protected MediaRepository $mediaRepository;
    protected UserRepository $userRepository;

    public function __construct()
    {
        $this->alertRepository = new AlertRepository(new Alert());
        $this->mediaRepository = new MediaRepository(new Media());
        $this->userRepository = new UserRepository(new User());
    }

    public function store()
    {
        return $this->alertRepository->store(request()->only(['user_id', 'media_id', 'status', 'type', 'reason']));
    }
}
