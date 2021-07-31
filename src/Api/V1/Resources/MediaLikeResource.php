<?php

namespace Aparlay\Core\Api\V1\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaLikeResource extends JsonResource
{
    use SimpleUserTrait;

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            '_id' => (string)$this->_id,
            'media_id' => (string)$this->media_id,
            'user_id' => (string)$this->user_id,
            'creator' => $this->createSimpleUser($this->creator),
            'created_at' => $this->created_at->valueOf(),
        ];
    }
}
