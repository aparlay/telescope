<?php

namespace Aparlay\Core\Api\V1\Models;

use Aparlay\Core\Models\User as UserBase;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

/**
 * User model
 *
 * @property ObjectId $_id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property bool $email_verified
 * @property string $phone_number
 * @property bool $phone_number_verified
 * @property string $auth_key
 * @property string $avatar
 * @property integer $status
 * @property integer $visibility
 * @property integer $block_count
 * @property integer $follower_count
 * @property integer $following_count
 * @property integer $like_count
 * @property integer $followed_hashtag_count
 * @property integer $media_count
 * @property UTCDateTime $created_at
 * @property UTCDateTime $updated_at
 * @property array $setting
 * @property array $features
 * @property-read mixed $authLogs
 * @property-read mixed $id
 * @property-write string $passwordHashField
 * @property-read string $authKey
 * @property-read array $links
 * @property-read bool $require_otp
 * @property-read bool $is_protected
 * @property array $defaultSetting
 *
 * @OA\Schema()
 *
 */
class User extends UserBase
{
}
