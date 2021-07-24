<?php

namespace Aparlay\Core\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Controllers;


class UserService
{
    public function requireOtp($user, $authType)
    {   
        $userSettingOtp = json_decode($user['setting'], true);
        if($userSettingOtp["otp"] || $user["status"] == User::STATUS_PENDING){
            if ($authType == 'phone_number') {
               //send sms otp
               return response()->json(['success' => true]);
                return "If you enter your phone number correctly you will receive an OTP sms soon";
            } elseif ($authType == 'email') {  
                //send email otp
                return "If you enter your email correctly you will receive an OTP email in your inbox soon.";
            }
        } else {
            return false;
        }
    
    }
}