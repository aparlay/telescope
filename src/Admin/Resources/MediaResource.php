<?php

namespace Aparlay\Core\Admin\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    use SimpleMediaTrait;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $medias = [];
        foreach ($this->resource as $media) {
            $medias[] = $this->createSimpleUser($media);
        }

        return [
            'aaData' => $medias,
            'iTotalDisplayRecords' => $this->total_filtered_media,
            'iTotalRecords' => $this->total_media,
        ];
    }
}
