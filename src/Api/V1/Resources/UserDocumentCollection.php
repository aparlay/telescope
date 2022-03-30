<?php

namespace Aparlay\Core\Api\V1\Resources;

use Aparlay\Core\Models\UserDocument;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @mixin UserDocument
 */
class UserDocumentCollection extends ResourceCollection
{
    public $collects = UserDocumentResource::class;

    public function toArray($request): array | Arrayable
    {
        $output = parent::toArray($request);
        foreach ($output as $item) {
            $output[$item['type']] = $item;
        }

        return array_values($output);
    }
}
