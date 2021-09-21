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
            'items' => $this->resource->items(),
            '_links' => [
                'first' => [
                    'href' => $this->resource->url($this->resource->onFirstPage()),
                ],
                'last' => [
                    'href' => $this->resource->url($this->resource->lastPage()),
                ],
            ],
            '_meta' => [
                'per_page' => $this->resource->perPage(),
                'current_page' => $this->resource->currentPage(),
                'page_count' => $this->resource->lastPage(),
                'total_count' => $this->resource->total(),
            ],
        ];
    }
}
