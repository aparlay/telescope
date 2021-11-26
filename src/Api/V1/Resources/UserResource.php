<?php

namespace Aparlay\Core\Api\V1\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        $user = auth()->user() ?: false;
        $isFollowed = false;
        $isBlocked = false;
        if ($user) {
            $followingIds = array_column($user->followings, '_id');
            $isFollowed = in_array((string) $this->_id, $followingIds);

            $blockedIds = array_column($user->blocks, '_id');
            $isBlocked = in_array((string) $this->_id, $blockedIds);
        }

        return [
            '_id' => (string) $this->_id,
            'username' => $this->username,
            'bio' => $this->bio,
            'full_name' => $this->full_name,
            'avatar' => $this->avatar,
            'visibility' => $this->visibility,
            'is_followed' => $isFollowed,
            'is_blocked' => $isBlocked,
            'promo_link' => $this->promo_link,
            'follower_count' => $this->follower_count,
            'following_count' => $this->following_count,
            'like_count' => $this->like_count,
            'tips' => $this->tips,
            'sent_tips' => $this->sent_tips,
            'created_at' => $this->created_at->valueOf(),
            'updated_at' => $this->updated_at->valueOf(),
            '_links' => [
                'self' => ['href' => route('core.api.v1.user.show', ['user' => $this])],
            ],
        ];
    }
}
