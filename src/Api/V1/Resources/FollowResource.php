<?php

namespace Aparlay\Core\Api\V1\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FollowResource extends JsonResource
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
            'creator' => $this->createSimpleUser($this->creator, ['_id', 'username', 'avatar', 'is_followed']),
            'user' => $this->createSimpleUser($this->user, ['_id', 'username', 'avatar', 'is_followed']),
            'created_at' => $this->created_at->valueOf(),
        ];
    }
}
