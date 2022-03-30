<?php

namespace Aparlay\Core\Api\V1\Resources;

use Aparlay\Core\Models\UserDocument;
use Illuminate\Contracts\Support\Arrayable;

/**
 * @mixin UserDocument
 */
class UserDocumentCollection extends AbstractResourceCollection
{
    public $collects = UserDocumentResource::class;

    public function toArray($request): array | Arrayable
    {
        $output = parent::toArray($request);
        $items = [];
        foreach ($this->resource->items() as $item) {
            $items[$item['type']] = $item;
        }

        $output['items'] = array_values($items);

        return $output;
    }
}
