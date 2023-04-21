<?php

namespace Aparlay\Core\Api\V1\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

abstract class AbstractResourceCollection extends ResourceCollection
{
    public function toArray($request): array|Arrayable|JsonSerializable
    {
        return $this->preparePagination();
    }

    private function preparePagination()
    {
        $links = $meta = [];

        if (method_exists($this->resource, 'perPage')) {
            $meta['per_page'] = $this->resource->perPage();
        }
        if (method_exists($this->resource, 'currentPage')) {
            $meta['current_page'] = $this->resource->currentPage();
        }
        if (method_exists($this->resource, 'lastPage')) {
            $meta['page_count'] = $this->resource->lastPage();
        }
        if (method_exists($this->resource, 'total')) {
            $meta['total_count'] = $this->resource->total();
        }

        if (method_exists($this->resource, 'firstPage') && $this->resource->onFirstPage() !== true) {
            $links['first'] = ['href' => $this->normalizeUrl($this->resource->url($this->resource->firstPage()))];
        }

        if (method_exists($this->resource, 'lastPage') && $this->resource->onLastPage() !== true) {
            $links['last'] = ['href' => $this->normalizeUrl($this->resource->url($this->resource->lastPage()))];
        }

        if (method_exists($this->resource, 'currentPage') && $this->resource->currentPage()) {
            $links['self'] = ['href' => $this->normalizeUrl($this->resource->url($this->resource->currentPage()))];
        }

        if (method_exists($this->resource, 'cursor')) {
            $links['self'] = ['href' => $this->normalizeUrl($this->resource->url($this->resource->cursor()))];
        }

        if (!$this->resource->onFirstPage() && $this->resource->previousPageUrl()) {
            $links['prev'] = ['href' => $this->normalizeUrl($this->resource->previousPageUrl())];
        }

        if ($this->resource->hasMorePages() && $this->resource->nextPageUrl()) {
            $links['next'] = ['href' => $this->normalizeUrl($this->resource->nextPageUrl())];
        }

        return [
            'items' => $this->resource->items(),
            '_links' => $links,
            '_meta' => $meta,
        ];
    }

    public function normalizeUrl($url): array|string
    {
        $url = str_replace('http://', 'https://', $url);

        return str_replace(['api1', 'api2', 'api3', 'api4', 'api5'], 'api', $url);
    }
}
