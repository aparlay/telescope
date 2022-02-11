<?php

namespace Aparlay\Core\Api\V1\Resources;

use Aparlay\Core\Models\UserDocument;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @mixin UserDocument
 */
class UserDocumentCollection extends AbstractResourceCollection
{
    public $collects = UserDocumentResource::class;
}
