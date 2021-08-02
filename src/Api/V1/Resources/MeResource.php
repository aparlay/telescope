<?php

namespace Aparlay\Core\Api\V1\Resources;

use Aparlay\Core\Api\V1\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeResource extends JsonResource
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
            '_id' => (string)$this->_id,
            'username' => $this->username,
            'bio' => $this->bio,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'email_verified' => (bool)$this->email_verified,
            'phone_number' => $this->phone_number,
            'phone_number_verified' => (bool)$this->phone_number_verified,
            'avatar' => $this->avatar,
            'setting' => [
                'otp' => (bool)$this->setting['otp'],
                'notifications' => [
                    'unread_message_alerts' => (bool)$this->setting['notifications']['unread_message_alerts'],
                    'new_followers' => (bool)$this->setting['notifications']['new_followers'],
                    'news_and_updates' => (bool)$this->setting['notifications']['news_and_updates'],
                    'tips' => (bool)$this->setting['notifications']['tips'],
                    'new_subscribers' => (bool)$this->setting['notifications']['new_subscribers'],
                ],
            ],
            'features' => [
                'tips' => (bool)$this->features['tips'],
                'demo' => (bool)$this->features['demo'],
            ],
            'gender' => User::getGenders()[$this->gender],
            'interested_in' => User::getInterestedIns()[$this->interested_in],
            'status' => User::getStatuses()[$this->status],
            'visibility' => User::getVisibilities()[$this->visibility],
            'promo_link' => $this->promo_link,
            'follower_count' => (int)$this->follower_count,
            'following_count' => (int)$this->following_count,
            'like_count' => (int)$this->like_count,
            'block_count' => (int)$this->block_count,
            'followed_hashtag_count' => (int)$this->followed_hashtag_count,
            'media_count' => (int)$this->followed_hashtag_count,
            'is_followed' => false,
            'is_blocked' => false,
            'blocks' => [],
            'likes' => [],
            'followers' => [],
            'followings' => [],
            'medias' => [],
            'alerts' => AlertResource::collection($this->alerts),
            'created_at' => $this->created_at->valueOf(),
            'updated_at' => $this->updated_at->valueOf(),
            '_links' => [
                'self' => ['href' => route('core.api.v1.user.show', ['user' => $this])],
            ],
        ];
    }
}
