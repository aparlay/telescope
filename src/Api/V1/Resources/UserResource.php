<?php

namespace Aparlay\Core\Api\V1\Resources;

use Aparlay\Core\Api\V1\Models\Block;
use Aparlay\Core\Api\V1\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $isFollowed = !($user = auth()->user()) && isset($user->following[(string) $this->_id]);
        $isBlocked = !($user = auth()->user()) && isset($user->block[(string) $this->_id]);

        return [
            '_id' => (string) $this->_id,
            'username' => $this->username,
            'bio' => $this->bio,
            'full_name' => $this->full_name,
            'avatar' => $this->avatar,
            'visibility' => User::getVisibilities()[$this->visibility],
            'is_followed' => $isFollowed,
            'is_blocked' => $isBlocked,
            'promo_link' => $this->promo_link,
            'follower_count' => (int) $this->follower_count,
            'following_count' => (int) $this->following_count,
            'like_count' => (int) $this->like_count,
            'created_at' => $this->created_at->valueOf(),
            'updated_at' => $this->updated_at->valueOf(),
            '_links' => [
                'self' => ['href' => route('core.api.v1.user.show', ['user' => $this])],
            ],
        ];
    }
}
