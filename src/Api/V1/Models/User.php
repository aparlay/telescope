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
     * Get the user's full name.
     */
    public function getIsFollowedAttribute(): bool
    {
        if (auth()->guest()) {
            return false;
        }

        $cacheKey = (new Follow())->getCollection().':creator:'.auth()->user()->id;
        Follow::cacheByUserId(auth()->user()->id);

        return Redis::sismember($cacheKey, (string) $this->_id);
    }
}
