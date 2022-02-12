<?php

namespace Aparlay\Core\Api\V1\Swagger\Resources;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema()
 */
class User
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
}
