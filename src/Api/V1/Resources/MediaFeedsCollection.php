<?php

namespace Aparlay\Core\Api\V1\Resources;

use BeyondCode\ServerTiming\Facades\ServerTiming;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class MediaFeedsCollection extends ResourceCollection
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
     * @param  Request  $request
     *
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array | Arrayable | JsonSerializable
    {
        $links = [
            'prev' => ['href' => str_replace('http://', 'https://', $this->resource->url($this->resource->lastPage() - 1))],
            'first' => ['href' => $this->resource->url($this->resource->onFirstPage())],
            'last' => ['href' => $this->resource->url($this->resource->lastPage())],
            'self' => ['href' => $this->resource->url($this->resource->currentPage())],
            'next' => ['href' => str_replace('http://', 'https://', $this->resource->url($this->resource->onFirstPage()))], // because of the caching and removing visited videos in feed next page is always the first page
        ];


        ServerTiming::start('MediaFeedsCollection::generateItems');
        $items = $this->resource->items();
        ServerTiming::stop('MediaFeedsCollection::generateItems');

        return [
            'items' => $items,
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
