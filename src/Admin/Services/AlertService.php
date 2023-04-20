<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Core\Admin\Models\Alert;
use Aparlay\Core\Admin\Models\Media;
use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Admin\Repositories\AlertRepository;
use Aparlay\Core\Admin\Repositories\MediaRepository;
use Aparlay\Core\Admin\Repositories\UserRepository;
use Aparlay\Core\Api\V1\Traits\HasUserTrait;
use Aparlay\Core\Events\UserReceiveAlertEvent;
use Aparlay\Core\Models\Enums\AlertStatus;
use Aparlay\Core\Models\Enums\AlertType;
use Aparlay\Core\Models\Enums\NoteType;

class AlertService
{
    use HasUserTrait;
    protected AlertRepository $alertRepository;
    protected UserRepository $userRepository;
    protected MediaRepository $mediaRepository;

    public function __construct()
    {
        $this->alertRepository = new AlertRepository(new Alert());
        $this->mediaRepository = new MediaRepository(new Media());
        $this->userRepository  = new UserRepository(new User());
    }

    public function forUser($user, $reason)
    {
        $data = [
            'type' => AlertType::USER->value,
            'status' => AlertStatus::NOT_VISITED->value,
            'user_id' => $user->_id,
            'reason' => $reason,
        ];

        UserReceiveAlertEvent::dispatch($this->getUser(), $user, NoteType::WARNING_MESSAGE->value, $reason);

        return $this->alertRepository->create($data);
    }

    public function create()
    {
        if (request()->input('mediaStatus')) {
            $this->mediaRepository->update(['status' => request()->input('mediaStatus')], request()->input('media_id'));
        }
        $data = request()->only(['user_id', 'media_id', 'status', 'type', 'reason']);

        return $this->alertRepository->create($data);
    }
}
