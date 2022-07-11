<?php

namespace Aparlay\Core\Api\V1\Resources;

use Aparlay\Core\Models\Enums\UserNotificationCategory;
use Aparlay\Core\Models\UserDocument;
use Aparlay\Payment\Api\V1\Resources\TipResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin UserDocument
 */
class UserNotificationResource extends JsonResource
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
        $entity = match ($this->category) {
            UserNotificationCategory::COMMENTS->value, UserNotificationCategory::LIKES->value => new MediaResource($this->entityObj),
            UserNotificationCategory::FOLLOWS->value => new FollowResource($this->entityObj),
            UserNotificationCategory::TIPS->value => new TipResource($this->entityObj),
            default => [],
        };

        return [
            '_id' => (string) $this->_id,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'category' => $this->category,
            'category_label' => $this->category_label,
            'message' => $this->message,
            'payload' => $this->payload,
            'entity' => $this->entityObj,
            'created_at' => $this->created_at->valueOf(),
            'updated_at' => $this->updated_at->valueOf(),
        ];
    }
}
