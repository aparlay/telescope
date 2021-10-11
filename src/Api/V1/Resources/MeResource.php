<?php

namespace Aparlay\Core\Api\V1\Resources;

use Aparlay\Core\Api\V1\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeResource extends JsonResource
{
    use SimpleUserTrait;

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
        $followers = [];
        foreach ($this->followers as $follower) {
            $followers[] = $this->createSimpleUser($follower);
        }

        $likes = [];
        foreach ($this->likes as $like) {
            $likes[] = $this->createSimpleUser($like);
        }

        $followings = [];
        foreach ($this->followings as $following) {
            $followings[] = $this->createSimpleUser($following);
        }

        $blocks = [];
        foreach ($this->blocks as $block) {
            $blocks[] = $this->createSimpleUser($block);
        }

        return [
            '_id' => (string) $this->_id,
            'referral_id' => (string) $this->referral_id,
            'username' => $this->username,
            'bio' => $this->bio,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'email_verified' => $this->email_verified,
            'phone_number' => $this->phone_number,
            'phone_number_verified' => $this->phone_number_verified,
            'avatar' => $this->avatar,
            'setting' => [
                'otp' => $this->setting['otp'] ?? false,
                'notifications' => [
                    'unread_message_alerts' => $this->setting['notifications']['unread_message_alerts'] ?? false,
                    'new_followers' => $this->setting['notifications']['new_followers'] ?? false,
                    'news_and_updates' => $this->setting['notifications']['news_and_updates'] ?? false,
                    'tips' => $this->setting['notifications']['tips'] ?? false,
                    'new_subscribers' => $this->setting['notifications']['new_subscribers'] ?? false,
                ],
            ],
            'features' => [
                'tips' => $this->features['tips'] ?? false,
                'demo' => $this->features['demo'] ?? false,
            ],
            'gender' => $this->gender,
            'interested_in' => $this->interested_in,
            'status' => $this->status,
            'visibility' => $this->visibility,
            'promo_link' => $this->promo_link,
            'follower_count' => $this->follower_count,
            'following_count' => $this->following_count,
            'like_count' => $this->like_count,
            'block_count' => $this->block_count,
            'followed_hashtag_count' => $this->followed_hashtag_count,
            'media_count' => $this->followed_hashtag_count,
            'is_followed' => false,
            'is_blocked' => false,
            'blocks' => $blocks,
            'likes' => $likes,
            'followers' => $followers,
            'followings' => $followings,
            'medias' => $this->medias,
            'alerts' => AlertResource::collection($this->alerts),
            'created_at' => $this->created_at->valueOf(),
            'updated_at' => $this->updated_at->valueOf(),
            '_links' => [
                'self' => ['href' => url("/v1/user/view?id={$this->_id}")],
            ],
        ];
    }
}
