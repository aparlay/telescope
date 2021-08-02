<?php

namespace Aparlay\Core\Api\V1\Resources;

use Aparlay\Core\Api\V1\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
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
            '_id' => (string) $this->_id,
            'media_id' => (string) $this->media_id,
            'user_id' => (string) $this->user_id,
            'comment_id' => (string) $this->comment_id,
            'reason' => $this->reason,
            'type' => Report::getTypes()[$this->type],
            'status' => Report::getStatuses()[$this->status],
            'created_at' => $this->created_at->valueOf(),
            'updated_at' => $this->updated_at->valueOf(),
        ];
    }
}
