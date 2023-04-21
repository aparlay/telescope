<?php

namespace Aparlay\Core\Api\V1\Resources;

use Aparlay\Core\Api\V1\Models\MediaComment;
use Aparlay\Core\Api\V1\Services\MediaCommentLikeService;
use Aparlay\Core\Api\V1\Traits\FilterableResourceTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin MediaComment
 */
class MediaCommentResource extends JsonResource
{
    use FilterableResourceTrait;
    private MediaCommentLikeService $mediaCommentLikeService;

    public function __construct($resource)
    {
        parent::__construct($resource);

        $this->mediaCommentLikeService = app()->get(MediaCommentLikeService::class);

        if (auth()->check()) {
            $this->mediaCommentLikeService->setUser(auth()->user());
        }
    }

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
        $parentId = ($this->parent['_id'] ?? null);

        $isLiked  = false;
        if (auth()->check()) {
            $isLiked = $this->mediaCommentLikeService->isLikedByUser((string) $this->_id);
        }

        $data     = [
            '_id' => (string) $this->_id,
            'parent_id' => $parentId ? (string) $parentId : null,
            'media_id' => (string) $this->media_id,
            'text' => $this->text,
            'is_liked' => $isLiked,
            'likes_count' => $this->likes_count ?? 0,

            $this->mergeWhen(
                !$this->parentObj,
                fn () => [
                    'replies_count' => $this->replies_count ?? 0,
                    'first_reply' => $this->first_reply,
                ]
            ),
            $this->mergeWhen(
                !empty($this->parentObj),
                fn () => [
                    'reply_to_user' => $this->reply_to_user['username'] ?? null,
                ]
            ),
            'user_id' => (string) $this->user_id,
            'creator' => $this->creator,
            'created_at' => $this->created_at->valueOf(),
        ];

        return $this->filtrateFields($this->filter($data));
    }
}
