<?php

namespace Aparlay\Core\Api\V1\Resources;

use Aparlay\Core\Helpers\DT;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlertResource extends JsonResource
{
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
            'title' => $this->title,
            'reason' => $this->reason,
            'created_at' => DT::utcToTimestamp($this->created_at),
        ];
    }
}
