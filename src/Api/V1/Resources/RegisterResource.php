<?php

namespace Aparlay\Core\Api\V1\Resources;

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
            'visibility' => $this->visibility,
            'follower_count' => $this->stats['counters']['followers'],
            'following_count' => $this->stats['counters']['followings'],
            'like_count' => $this->stats['counters']['likes'],
            'block_count' => $this->stats['counters']['blocks'],
            'followed_hashtag_count' => $this->stats['counters']['followed_hashtags'],
            'media_count' => $this->stats['counters']['medias'],
            'has_unread_chat' => false,
            'has_unread_notification' => false,
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
