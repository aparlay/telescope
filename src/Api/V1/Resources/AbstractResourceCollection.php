<?php

namespace Aparlay\Core\Api\V1\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

abstract class AbstractResourceCollection extends ResourceCollection
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
        $links = [
            'first' => ['href' => $this->normalizeUrl($this->resource->url($this->resource->onFirstPage()))],
            'last' => ['href' => $this->normalizeUrl($this->resource->url($this->resource->lastPage()))],
            'self' => ['href' => $this->normalizeUrl($this->resource->url($this->resource->currentPage()))],
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

    public function normalizeUrl($url): array|string
    {
        $url = str_replace('http://', 'https://', $url);
        $url = str_replace(['api1', 'api2', 'api3', 'api4', 'api5'], 'api', $url);

        return $url;
    }
}
