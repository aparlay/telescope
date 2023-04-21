<?php

namespace Aparlay\Core\Admin\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => $this->resource,
            'recordsFiltered' => $this->total_filtered_settings,
            'recordsTotal' => $this->total_settings,
        ];
    }
}
