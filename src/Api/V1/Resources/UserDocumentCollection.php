<?php

namespace Aparlay\Core\Api\V1\Resources;

use Aparlay\Core\Models\UserDocument;

/**
 * @mixin UserDocument
 */
class UserDocumentCollection extends AbstractResourceCollection
{
    public $collects = UserDocumentResource::class;
}
