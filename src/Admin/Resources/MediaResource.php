<?php

namespace Aparlay\Core\Admin\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => $this->resource,
            'recordsFiltered' => $this->total_filtered_media,
            'recordsTotal' => $this->total_media,
        ];
    }
}
