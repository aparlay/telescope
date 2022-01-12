<?php

namespace Aparlay\Core\Api\V1\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

abstract class AbstractResourceCollection  extends ResourceCollection
{

    public function toArray($request): array | Arrayable | JsonSerializable
    {
        return $this->preparePagination();
    }

    private function preparePagination()
    {
        $links = [
            'first' => ['href' => $this->resource->url($this->resource->onFirstPage())],
            'last' => ['href' => $this->resource->url($this->resource->lastPage())],
            'self' => ['href' => $this->resource->url($this->resource->currentPage())],
        ];

        if ($this->resource->previousPageUrl()) {
            $links['prev'] = [
                'href' =>  $this->resource->previousPageUrl(),
            ];
        }

        if ($this->resource->nextPageUrl()) {
            $links['next'] = [
                'href' =>  $this->resource->nextPageUrl(),
            ];
        }

        $return = [
            'items' => $this->resource->items(),
            '_links' => $links,
            '_meta' => [
                'per_page' => $this->resource->perPage(),
                'current_page' => $this->resource->currentPage(),
                'page_count' => $this->resource->lastPage(),
                'total_count' => $this->resource->total(),
            ],
        ];

        return $return;
    }
}
