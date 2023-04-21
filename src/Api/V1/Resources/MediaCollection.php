<?php

namespace Aparlay\Core\Api\V1\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use JsonSerializable;

class MediaCollection extends AbstractResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = MediaResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     */
    public function toArray($request): array|Arrayable|JsonSerializable
    {
        $links = [
            'first' => ['href' => $this->resource->url($this->resource->onFirstPage())],
            'last' => ['href' => $this->resource->url($this->resource->lastPage())],
            'self' => ['href' => $this->resource->url($this->resource->currentPage())],
        ];

        if ($this->resource->previousPageUrl()) {
            $links['prev'] = [
                'href' => $this->normalizeUrl($this->resource->previousPageUrl()),
            ];
        }

        if ($this->resource->nextPageUrl()) {
            $links['next'] = [
                'href' => $this->normalizeUrl($this->resource->nextPageUrl()),
            ];
        }

        return [
            'items' => $this->resource->items(),
            '_links' => $links,
            '_meta' => [
                'per_page' => $this->resource->perPage(),
                'current_page' => $this->resource->currentPage(),
                'page_count' => $this->resource->lastPage(),
                'total_count' => $this->resource->total(),
            ],
        ];
    }
}
