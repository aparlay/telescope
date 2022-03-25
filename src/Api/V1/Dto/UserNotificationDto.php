<?php

namespace Aparlay\Core\Api\V1\Dto;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class UserNotificationDto extends DataTransferObject
{
    public $entity_type;
    public $entity_id;
    public $entity;
    public $category;
    public $status;
    public $message;
    public $user_id;
    private $user;

    /**
     * @param  array  $data
     * @return static
     * @throws UnknownProperties
     */
    public static function fromArray(array $data): self
    {
        $data['entity']['_id'] = $data['entity_id'];
        $data['entity']['_type'] = $data['entity_type'];
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
