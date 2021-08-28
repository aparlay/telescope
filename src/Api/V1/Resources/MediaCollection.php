<?php

namespace Aparlay\Core\Api\V1\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class MediaCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     *
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array | Arrayable | JsonSerializable
    {
        return [
            'items' => $this->collection,
            '_links' => [
                'next' => [
                    'href' => $this->resource->nextPageUrl(),
                ],
                'prev' => [
                    'href' => $this->resource->previousPageUrl(),
                ],
            ],
            '_meta' => [
                'per_page' => $this->resource->perPage(),
                'current_page' => $this->resource->currentPage(),
                'page_count' => $this->resource->count(),
                'total_count' => $this->resource->total(),
            ],
        ];
    }
}
