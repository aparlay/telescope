<?php

namespace Aparlay\Core\Api\V1\Resources;

use Aparlay\Core\Admin\Resources\UserResource;
use Aparlay\Core\Api\V1\Models\MediaComment;
use Aparlay\Core\Api\V1\Traits\FilterableResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin MediaComment
 */
class MediaCommentResource extends JsonResource
{
    use FilterableResourceTrait;

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
        $data = [
            '_id' => (string) $this->_id,
            'parent_id' => $this->parent_id ? (string) $this->parent_id : null,
            'media_id' => (string) $this->media_id,
            'text' => $this->text,
            'likes_count' => $this->likes_count ?? 0,
            $this->mergeWhen(
                ! $this->parent,
                fn () => [
                    'replies_count' => $this->replies_count,
                    'replies' => new MediaCommentReplyCollection($this->lastRepliesObjs),
                ]
            ),
            'user_id' => (string) $this->user_id,
            'creator' => $this->creator,
            'created_at' => $this->created_at->valueOf(),
        ];

        return $this->filtrateFields($this->filter($data));
    }
}
