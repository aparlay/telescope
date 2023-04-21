<?php

namespace Aparlay\Core\Api\V1\Resources;

class UserCollection extends AbstractResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = UserResource::class;
}
