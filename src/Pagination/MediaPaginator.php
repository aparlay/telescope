<?php

namespace Aparlay\Core\Pagination;

use Aparlay\Core\Api\V1\Resources\MediaCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class MediaPaginator extends LengthAwarePaginator
{
    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'items' => new MediaCollection($this->items),
            '_links' => [
                'self' => [
                    'href' => $this->url($this->currentPage()),
                ],
                'first' => [
                    'href' => $this->url(1),
                ],
                'last' => [
                    'href' => $this->url($this->lastPage()),
                ],
                'next' => [
                    'href' => $this->nextPageUrl(),
                ],
                'prev' => [
                    'href' => $this->previousPageUrl(),
                ],
            ],
            '_meta' => [
                'total_count' => $this->total(),
                'page_count' => $this->lastPage(),
                'current_page' => $this->currentPage(),
                'per_page' => $this->perPage(),
            ],
        ];
    }
}
