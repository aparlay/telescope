<?php

namespace Aparlay\Core\Api\V1\Resources;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserNotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @throws Exception
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            '_id' => (string) $this->_id,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'category' => $this->category,
            'category_label' => $this->category_label,
            'message' => $this->message,
            'payload' => $this->payload,
            'created_at' => $this->created_at->valueOf(),
            'updated_at' => $this->updated_at->valueOf(),
        ];
    }
}
