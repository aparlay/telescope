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
        $request = request()->only(['type', 'reason']);

        $data = [
            'user_id' => new ObjectId($this->userRepository->find(request()->input('user_id'))->_id),
            'media_id' => request()->input('media_id') ? new ObjectId($this->mediaRepository->find(request()->input('media_id'))->_id) : null,
            'status' => Alert::STATUS_NOT_VISITED
        ];

        $alertData = array_merge($request, $data);

        return $this->alertRepository->store($alertData);
    }
}
