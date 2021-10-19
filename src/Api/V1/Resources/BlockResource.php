<?php

namespace Aparlay\Core\Api\V1\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlockResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     *
     * @return array
     * @throws \Exception
     */
    public function toArray($request)
    {
        return [
            '_id' => (string) $this->_id,
            'creator' => $this->creator,
            'user' => $this->user,
            'created_at' => $this->created_at->valueOf(),
        ];
    }
}
