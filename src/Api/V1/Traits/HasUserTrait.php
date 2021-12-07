<?php
namespace Aparlay\Core\Api\V1\Traits;
use Aparlay\Core\Api\V1\Models\User;

trait HasUserTrait
{

    /**
     * @var $user User
     */
    protected $user;


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
