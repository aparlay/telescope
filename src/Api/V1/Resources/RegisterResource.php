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
            'username' => (string) $this->username,
            'full_name' => (string) $this->full_name,
            'email' => (string) $this->email,
            'email_verified' => $this->email_verified,
            'phone_number' => $this->phone_number,
            'phone_number_verified' => $this->phone_number_verified,
            'status' => User::getStatuses()[$this->status],
            'bio' => $this->bio,
            'avatar' => (string) $this->avatar,
            'setting' => $this->setting,
            'features' => $this->features,
            'gender' => User::getGenders()[$this->gender],
            'interested_in' => User::getInterestedIns()[$this->interested_in],
            'visibility' => User::getVisibilities()[$this->visibility],
            'block_count' => (int) $this->block_count,
            'follower_count' => (int) $this->follower_count,
            'following_count' => (int) $this->following_count,
            'like_count' => (int) $this->like_count,
            'followed_hashtag_count' => (int) $this->followed_hashtag_count,
            'media_count' => (int) $this->media_count,
            'is_followed' => false,
            'is_blocked' => false,
            'promo_link' => $this->promo_link,
            'followers' => [],
            'followings' => [],
            'medias' => [],
            'created_at' => $this->created_at->valueOf(),
            'updated_at' => $this->updated_at->valueOf(),
            '_links' => [
                'self' => ['href' => route('core.api.v1.user.show', ['user' => $this])],
            ],
        ];
    }
}