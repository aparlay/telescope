<?php

namespace Aparlay\Core\Api\V1\Swagger\Resources;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema()
 */
class Me
{
    /**
     * @OA\Property(property="_id", type="string", example="60237caf5e41025e1e3c80b1")
     */
    public $_id;

    /**
     * @OA\Property(property="referral_id", type="string", example="60237caf5e41025e1e3c80b1")
     */
    public $referral_id;

    /**
     * @OA\Property(property="username", type="string", example="john")
     */
    public $username;

    /**
     * @OA\Property(property="full_name", type="string", example="John Walker")
     */
    public $full_name;

    /**
     * @OA\Property(property="email", type="string", example="john@waptap.com")
     */
    public $email;

    /**
     * @OA\Property(property="email_verified", type="boolean", example=true)
     */
    public $email_verified;

    /**
     * @OA\Property(property="phone_number", type="string", example="+989153023386")
     */
    public $phone_number;

    /**
     * @OA\Property(property="phone_number_verified", type="boolean", example=true)
     */
    public $phone_number_verified;

    /**
     * @OA\Property(property="status", type="integer", description="pending=0, verified=0, active=2, suspended=3, blocked=4", example=1)
     */
    public $status;

    /**
     * @OA\Property(property="bio", type="string", example="My boo boo is the best!")
     */
    public $bio;

    /**
     * @OA\Property(property="avatar", type="string", example="https://assets.waptap.com/avatars/60237dacc7dd4171920af0e9_602a1aca94494.jpg")
     */
    public $avatar;

    /**
     * @OA\Property(property="setting", type="object", example="{otp: true}")
     */
    public $setting;

    /**
     * @OA\Property(property="features", type="object", example="{tips: true}")
     */
    public $features;

    /**
     * @OA\Property(property="gender", type="integer", description="female=0, male=1, trans=2, not_mention=3", example=1)
     */
    public $gender;

    /**
     * @OA\Property(property="visibility", type="integer", description="private=0, public=1", example=1)
     */
    public $visibility;

    /**
     * @OA\Property(property="block_count", type="integer", example=2323)
     */
    public $block_count;

    /**
     * @OA\Property(property="follower_count", type="integer", example=2323)
     */
    public $follower_count;

    /**
     * @OA\Property(property="following_count", type="integer", example=2323)
     */
    public $following_count;

    /**
     * @OA\Property(property="has_unread_chat", type="bool", example=true)
     */
    public $has_unread_chat;

    /**
     * @OA\Property(property="has_unread_notification", type="bool", example=true)
     */
    public $has_unread_notification;

    /**
     * @OA\Property(property="is_followed", type="boolean", example=false)
     */
    public $is_followed;

    /**
     * @OA\Property(property="is_blocked", type="boolean", example=false)
     */
    public $is_blocked;

    /**
     * @OA\Property(property="like_count", type="integer", example=2323)
     */
    public $like_count;

    /**
     * @OA\Property(property="followed_hashtag_count", type="integer", example=2323)
     */
    public $followed_hashtag_count;

    /**
     * @OA\Property(property="media_count", type="integer", example=23)
     */
    public $media_count;

    /**
     * @OA\Property(property="payout_country_alpha2", type="string", example="us")
     */
    public $payout_country_alpha2;

    /**
     * @OA\Property(property="country_alpha2", type="string", example="US")
     */
    public $country_alpha2;

    /**
     * @OA\Property(property="country_alpha3", type="string", example="USA")
     */
    public $country_alpha3;

    /**
     * @OA\Property(property="country_label", type="string", example="United State of America")
     */
    public $country_label;

    /**
     * @OA\Property(property="payout_country_label", type="string", example="United State of America")
     */
    public $payout_country_label;

    /**
     * @OA\Property(property="country_flags", type="array", @OA\Items ())
     */
    public $country_flags;

    /**
     * @OA\Property(property="followers", type="array", @OA\Items (ref="#/components/schemas/User"))
     */
    public $followers;

    /**
     * @OA\Property(property="followings", type="array", @OA\Items (ref="#/components/schemas/User"))
     */
    public $followings;

    /**
     * @OA\Property(property="medias", type="array", @OA\Items (ref="#/components/schemas/Media"))
     */
    public $medias;

    /**
     * @OA\Property(property="created_at", type="number", example=1612850111566)
     */
    public $created_at;

    /**
     * @OA\Property(property="updated_at", type="number", example=1612850111566)
     */
    public $updated_at;
}
