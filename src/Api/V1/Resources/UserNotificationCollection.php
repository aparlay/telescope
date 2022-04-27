<?php

namespace Aparlay\Core\Api\V1\Resources;

use Aparlay\Core\Models\UserDocument;

/**
 * @mixin UserDocument
 */
class UserNotificationCollection extends AbstractResourceCollection
{
    public $collects = UserNotificationResource::class;
}
