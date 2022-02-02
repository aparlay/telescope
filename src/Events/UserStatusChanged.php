<?php

namespace Aparlay\Core\Events;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserStatusChanged implements ShouldQueue
{
    use Dispatchable;
    use SerializesModels;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        private $creator,
        private $newUserStatus,
        private $userId,
    ){}

    /**
     * @return mixed
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * @return mixed
     */
    public function getNewUserStatus()
    {
        return $this->newUserStatus;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }


}
