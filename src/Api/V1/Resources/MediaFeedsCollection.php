<?php

namespace Aparlay\Core\Api\V1\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use JsonSerializable;

class MediaFeedsCollection extends AbstractResourceCollection
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
            'prev' => ['href' => $this->normalizeUrl($this->resource->url($this->resource->lastPage() - 1))],
            'first' => ['href' => $this->resource->url($this->resource->onFirstPage())],
            'last' => ['href' => $this->resource->url($this->resource->lastPage())],
            'self' => ['href' => $this->resource->url($this->resource->currentPage())],
            'next' => ['href' => $this->normalizeUrl($this->resource->url($this->resource->onFirstPage()))], // because of the caching and removing visited videos in feed next page is always the first page
        ];

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
