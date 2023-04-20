<?php

namespace Aparlay\Core\Api\V1\Models;

use Aparlay\Core\Models\User as UserBase;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

/**
 * User model.
 *
 * @property ObjectId    $_id
 * @property string      $auth_key
 * @property string      $authKey
 * @property mixed       $authLogs
 * @property string      $avatar
 * @property string      $bio
 * @property array       $count_fields_updated_at
 * @property string      $country_alpha2
 * @property string      $country_flag
 * @property array       $country_flags
 * @property string      $country_label
 * @property UTCDateTime $created_at
 * @property string      $deactivation_reason
 * @property array       $default_setting
 * @property string      $email
 * @property bool        $email_verified
 * @property array       $features
 * @property string      $full_name
 * @property int         $gender
 * @property mixed       $id
 * @property bool        $is_protected
 * @property array       $user_agents
 * @property array       $stats
 * @property array       $last_location
 * @property UTCDateTime $last_online_at
 * @property array       $links
 * @property string      $password_hash
 * @property string      $password_hash_field
 * @property string      $password_reset_token
 * @property string      $payout_country_alpha2
 * @property string      $phone_number
 * @property bool        $phone_number_verified
 * @property bool        $require_otp
 * @property array       $scores
 * @property array       $setting
 * @property int         $show_online_status
 * @property int         $status
 * @property array       $subscription_plan
 * @property array       $subscriptions
 * @property array       $text_search
 * @property UTCDateTime $updated_at
 * @property string      $username
 * @property int         $verification_status
 * @property int         $visibility
 * @property-read string $admin_url
 * @property-read bool   $is_online
 * @property-read bool   $is_online_for_followers
 * @property-read bool   $is_subscribable
 * @property-read string $slack_admin_url
 */
class User extends UserBase
{
    use HasPushSubscriptions;
}
