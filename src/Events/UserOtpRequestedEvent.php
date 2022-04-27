<?php

namespace Aparlay\Core\Events;

use Aparlay\Core\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserOtpRequestedEvent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        protected Authenticatable|User $user,
        protected $deviceId
    ) {
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getDeviceId()
    {
        return $this->deviceId;
    }
}
