<?php

namespace Aparlay\Core\Api\V1\Resources;

use Aparlay\Core\Api\V1\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegisterResource extends JsonResource
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
        return [
            '_id' => (string) $this->_id,
            'referral_id' => (string) $this->referral_id,
            'username' => $this->username,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'email_verified' => $this->email_verified,
            'phone_number' => $this->phone_number,
            'phone_number_verified' => $this->phone_number_verified,
            'status' => $this->status,
            'bio' => $this->bio,
            'avatar' => $this->avatar,
            'setting' => $this->setting,
            'features' => $this->features,
            'gender' => $this->gender,
            'interested_in' => $this->interested_in,
            'visibility' => $this->visibility,
            'block_count' => $this->block_count,
            'follower_count' => $this->follower_count,
            'following_count' => $this->following_count,
            'like_count' => $this->like_count,
            'followed_hashtag_count' => $this->followed_hashtag_count,
            'media_count' => $this->media_count,
            'is_followed' => false,
            'is_blocked' => false,
            'promo_link' => $this->promo_link,
            'blocks' => $this->blocks,
            'likes' => $this->likes,
            'followers' => $this->followers,
            'followings' => $this->followings,
            'medias' => $this->medias,
            'created_at' => $this->created_at->valueOf(),
            'updated_at' => $this->updated_at->valueOf(),
            '_links' => [
                'self' => ['href' => route('core.api.v1.user.show', ['user' => $this])],
            ],
        ];
    }
}
