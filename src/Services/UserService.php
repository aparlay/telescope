<?php

namespace Aparlay\Core\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Controllers;

use Illuminate\Validation\ValidationException;
use Validator;


class UserService
{
    /**
     * Check user status 
     */
    public function isUserEligible($user)
    {
        if ($user['status'] === User::STATUS_SUSPENDED) {
            return "This account has been suspended.";
        }
        if ($user['status'] === User::STATUS_BLOCKED) {
            return "This account has been banned.";
        }
        if ($user['status'] === User::STATUS_DEACTIVATED) {
            return "Your user account not found or does not match with password!";
        }
        return false;
    }

    /**
     * send otp if status in pending and request otp is null
     */
    public function sendOtp($user, $loginEntity)
    {
        if ($loginEntity == 'phone_number') {
            $result = [
                'message'     => 'If you enter your phone number correctly you will receive an OTP sms soon.',
                'sms_numbers' => $user['phone_number'],
                // 'sms_content' => Yii::t('app', Yii::$app->params['sms']['messages']['otp']),
            ];

            $otp = (new Otp())->generate($this->phone_number, $this->device_id);

            if (!$otp->hasErrors()) {
                return $otp->send();
            }

        } elseif ($loginEntity == 'email') {
            $result = [
                'message' => 'If you enter your email correctly you will receive an OTP email in your inbox soon.',
            ];
            
            $otp = (new Otp())->generate($this->email, $this->device_id);
            if (!$otp->hasErrors()) {
                return $otp->send();
            }
        }
    }
}