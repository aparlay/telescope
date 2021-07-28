<?php

namespace Aparlay\Core\Services;

use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Repositories\UserRepository;
use Aparlay\Core\Services\OtpService;
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
     * Responsible for returning Login Entity (email or phone_number or username) based on the input username
     * @param string $identity
     * @return string
     */
    public static function findIdentity($identity)
    {
        /** Find identity */
        switch($identity) {
            case filter_var( $identity, FILTER_VALIDATE_EMAIL ):
                return "email";
            case is_numeric($identity):
                return "phone_number";
            default:
                return "username";
        }
    }

    /**
     * send otp if status in pending and request otp is null
     * @param array $user
     * @param string $loginEntity
     * @param string $deviceId
     */
    public function requireOtp($user, $loginEntity, $deviceId)
    {
        if ($loginEntity == 'phone_number') {
            $result = [
                'message'     => 'If you enter your phone number correctly you will receive an OTP sms soon.',
                'sms_numbers' => $user['phone_number'],
                // 'sms_content' => Yii::t('app', Yii::$app->params['sms']['messages']['otp']),
            ];

        } elseif ($loginEntity == 'email') {
            $result = [
                'message' => 'If you enter your email correctly you will receive an OTP email in your inbox soon.',
            ];
        }

        if ($this->sendOtp($loginEntity, $user, $deviceId)) {
            //Yii::$app->response->statusCode = 418;
            return $result;
        }
    }

    /**
     * @return bool
     * @param string $loginEntity
     * @param array $user
     * @param string $deviceId
     */
    public function sendOtp($loginEntity, $user, $deviceId)
    {
        return ($loginEntity == 'phone_number')? $this->sendSmsOtp($user['phone_number'], $deviceId) : $this->sendEmailOtp($user['email'], $deviceId);
    }

     /**
     * Logs in a user using the provided phone_number.
     *
     * @return bool whether the user is sending otp in successfully
     * @param string $userEmail
     * @param string $deviceId
     */
    public function sendEmailOtp($userEmail, $deviceId)
    {
        // if ($this->validate()) {
            $user = UserRepository::findByEmail($userEmail);
            if ($user !== null) {
                $otp = OtpService::generate($userEmail, $deviceId);
                if ($otp) {
                    return OtpService::send($otp);
                }

                $this->addErrors($otp->errors);
            }
            return true;
        // }

        // return false;
    }

    /**
     * Logs in a user using the provided phone_number.
     *
     * @return bool whether the user is sending otp in successfully
     * @param number $userMobile
     * @param string $deviceId
     */
    public function sendSmsOtp($userMobile, $deviceId)
    {
        // if ($this->validate()) {
            $user = UserRepository::findByPhoneNumber($this->phone_number);
            if ($user !== null) {
                $otp = OtpService::generate($this->phone_number, $this->device_id);

                if ($otp) {
                    return OtpService::send($otp);
                }

                $this->addErrors($otp->errors);
            }
            return true;
        // }

        // return false;
    }

    /**
     * @param string $otp
     */
    public static function send($otp)
    {
        if ($otp['type'] === Otp::TYPE_EMAIL) {
            $this->sendByEmail($otp);
        } else {
            $this->sendBySMS($otp);
        }

        return true;
    }
}
