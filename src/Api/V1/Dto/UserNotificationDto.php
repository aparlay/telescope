<?php

namespace Aparlay\Core\Api\V1\Dto;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class UserNotificationDto extends DataTransferObject
{
    public $usernotifiable_type;
    public $usernotifiable_id;
    public $category;
    public $status;
    public $user_id;
    private $user;

    /**
     * @param  array  $data
     * @return static
     * @throws UnknownProperties
     */
    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }
}
