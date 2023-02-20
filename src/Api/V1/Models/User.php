<?php

namespace Aparlay\Core\Api\V1\Models;

use Aparlay\Core\Models\User as UserBase;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

/**
 * User model.
 *
 * @property ObjectId    $_id
 * @property string      $username
 * @property string      $full_name
 * @property string      $password_hash
 * @property string      $password_reset_token
 * @property string      $email
 * @property bool        $email_verified
 * @property string      $phone_number
 * @property bool        $phone_number_verified
 * @property string      $auth_key
 * @property string      $bio
 * @property string      $avatar
 * @property int         $status
 * @property int         $gender
 * @property int         $visibility
 * @property int         $show_online_status
 * @property UTCDateTime $created_at
 * @property UTCDateTime $updated_at
 * @property array       $setting
 * @property array       $features
 * @property mixed       $authLogs
 * @property mixed       $id
 * @property string      $password_hash_field
 * @property string      $authKey
 * @property array       $links
 * @property bool        $require_otp
 * @property bool        $is_protected
 * @property array       $default_setting
 * @property array       $count_fields_updated_at
 * @property array       $subscriptions
 * @property array       $user_agents
 * @property array       $stats
 * @property array       $last_location
 * @property string      $country_alpha2
 * @property string      $payout_country_alpha2
 * @property string      $country_label
 * @property string      $country_flag
 * @property array       $country_flags
 * @property array       $text_search
 * @property int         $verification_status
 * @property array       $scores
 * @property string      $deactivation_reason
 * @property UTCDateTime $last_online_at
 *
 * @property-read string $admin_url
 * @property-read string $slack_admin_url
 * @property-read bool   $is_subscribable
 * @property-read bool   $is_online
 * @property-read bool   $is_online_for_followers
 */
class User extends UserBase
{
}
