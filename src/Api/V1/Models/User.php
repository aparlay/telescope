<?php

namespace Aparlay\Core\Api\V1\Models;

use Aparlay\Core\Models\Follow;
use Aparlay\Core\Models\User as UserBase;
use Illuminate\Support\Facades\Redis;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

/**
 * User model.
 *
 * @property ObjectId    $_id
 * @property string      $username
 * @property string      $password_hash
 * @property string      $password_reset_token
 * @property string      $email
 * @property bool        $email_verified
 * @property string      $phone_number
 * @property bool        $phone_number_verified
 * @property string      $auth_key
 * @property string      $avatar
 * @property int         $status
 * @property int         $visibility
 * @property int         $block_count
 * @property int         $follower_count
 * @property int         $following_count
 * @property int         $like_count
 * @property int         $followed_hashtag_count
 * @property int         $media_count
 * @property UTCDateTime $created_at
 * @property UTCDateTime $updated_at
 * @property array       $setting
 * @property array       $features
 * @property mixed       $authLogs
 * @property mixed       $id
 * @property string      $passwordHashField
 * @property string      $authKey
 * @property array       $links
 * @property bool        $require_otp
 * @property bool        $is_protected
 * @property array       $defaultSetting
 *
 * @property-read string $admin_url
 * @property-read string $slack_admin_url
 * @property-read bool $is_followed
 *
 * @OA\Schema()
 */
class User extends UserBase
{
    /**
     * @OA\Property(property="_id", type="string", example="60237caf5e41025e1e3c80b1")
     * @OA\Property(property="referral_id", type="string", example="60237caf5e41025e1e3c80b1")
     * @OA\Property(property="username", type="string", example="john")
     * @OA\Property(property="full_name", type="string", example="John Walker")
     * @OA\Property(property="email", type="string", example="john@waptap.com")
     * @OA\Property(property="email_verified", type="boolean", example=true)
     * @OA\Property(property="phone_number", type="string", example="+989153023386")
     * @OA\Property(property="phone_number_verified", type="boolean", example=true)
     * @OA\Property(property="status", type="integer", description="pending=0, verified=0, active=2, suspended=3, blocked=4", example=1)
     * @OA\Property(property="bio", type="string", example="My boo boo is the best!")
     * @OA\Property(property="avatar", type="string", example="https://assets.waptap.com/avatars/60237dacc7dd4171920af0e9_602a1aca94494.jpg")
     * @OA\Property(property="setting", type="object", example="{otp: true}")
     * @OA\Property(property="features", type="object", example="{tips: true}")
     * @OA\Property(property="gender", type="integer", description="female=0, male=1, trans=2, not_mention=3", example=1)
     * @OA\Property(property="interested_in", type="integer", description="female=0, male=1, trans=2, couple=3", example=1)
     * @OA\Property(property="visibility", type="integer", description="private=0, public=1", example=1)
     * @OA\Property(property="block_count", type="integer", example=2323)
     * @OA\Property(property="follower_count", type="integer", example=2323)
     * @OA\Property(property="following_count", type="integer", example=2323)
     * @OA\Property(property="is_followed", type="boolean", example=false)
     * @OA\Property(property="is_blocked", type="boolean", example=false)
     * @OA\Property(property="like_count", type="integer", example=2323)
     * @OA\Property(property="followed_hashtag_count", type="integer", example=2323)
     * @OA\Property(property="media_count", type="integer", example=23)
     * @OA\Property(property="followers", type="array", @OA\Items (ref="#/components/schemas/User"))
     * @OA\Property(property="followings", type="array", @OA\Items (ref="#/components/schemas/User"))
     * @OA\Property(property="medias", type="array", @OA\Items (ref="#/components/schemas/Media"))
     * @OA\Property(property="created_at", type="number", example=1612850111566)
     * @OA\Property(property="updated_at", type="number", example=1612850111566)
     */

    /**
     * Get the user's full name.
     */
    public function getIsFollowedAttribute(): bool
    {
        if (auth()->guest()) {
            return false;
        }

        $cacheKey = (new Follow())->getCollection().':creator:'.auth()->user()->_id;
        Follow::cacheByUserId(auth()->user()->_id);

        return Redis::sismember($cacheKey, (string) $this->_id);
    }
}
