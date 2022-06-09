<?php

namespace Aparlay\Core\Api\V1\Resources;

use Aparlay\Core\Api\V1\Models\MediaComment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin MediaComment
 */
class MediaCommentResource extends JsonResource
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
            'media_id' => (string) $this->media_id,
            'replies' => new MediaCommentReplyCollection($this->replies),
            'text' => $this->text,
            'user_id' => (string) $this->user_id,
            'creator' => $this->creator,
            'created_at' => $this->created_at->valueOf(),
        ];
    }
}
