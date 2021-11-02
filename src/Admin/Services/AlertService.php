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
        $this->mediaRepository = new MediaRepository(new Media());
    }

    public function create()
    {
        if (request()->input('mediaStatus')) {
            $this->mediaRepository->update(['status' => request()->input('mediaStatus')], request()->input('media_id'));
        }

        return $this->alertRepository->create(request()->only(['user_id', 'media_id', 'status', 'type', 'reason']));
    }
}
