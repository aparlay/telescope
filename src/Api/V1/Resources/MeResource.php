<?php

namespace Aparlay\Core\Api\V1\Resources;

use Aparlay\Core\Api\V1\Models\UserNotification;
use Aparlay\Core\Models\Enums\UserVerificationStatus;
use Aparlay\Core\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class MeResource extends JsonResource
{
    use SimpleUserTrait;
    use SimpleMediaTrait;

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

        $medias = [];
        foreach ($this->medias as $media) {
            $medias[] = $this->createSimpleMedia($media);
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
            'verification_status' => $this->verification_status,
            'verification_status_label' => $this->verification_status_label,
            'visibility' => $this->visibility,
            'promo_link' => $this->promo_link,
            'follower_count' => $this->counters['followers'] ?? 0,
            'following_count' => $this->counters['followings'] ?? 0,
            'like_count' => $this->counters['likes'] ?? 0,
            'block_count' => $this->counters['blocks'] ?? 0,
            'followed_hashtag_count' => $this->counters['followed_hashtags'] ?? 0,
            'media_count' => $this->counters['medias'] ?? 0,
            'has_unread_chat' => $this->has_unread_chat,
            'has_unread_notification' => $this->has_unread_notification,
            'is_followed' => false,
            'is_blocked' => false,
            'is_online' => true,
            'blocks' => $blocks,
            'likes' => $likes,
            'followers' => $followers,
            'followings' => $followings,
            'medias' => $medias,
            'alerts' => AlertResource::collection($this->alerts),
            'created_at' => $this->created_at->valueOf(),
            'updated_at' => $this->updated_at->valueOf(),
            '_links' => [
                'self' => ['href' => url("/v1/user/view?id={$this->_id}")],
            ],
            'is_verified' => $this->is_verified,
            'country_alpha3' => $this->country_alpha3,
            'payout_country_label' => $this->payout_country_label,
            'payout_country_alpha2' => $this->payout_country_alpha2,
            'country_alpha2' => $this->country_alpha2,
            'country_label' => $this->country_label,
            'country_flags' => $this->country_flags,
            $this->mergeWhen($this->is_tier1, fn () => ['is_tier1' => true]),
            $this->mergeWhen($this->is_tier3, fn () => ['is_tier3' => true]),
        ];
    }
}
