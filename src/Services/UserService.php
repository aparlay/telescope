<?php

namespace Aparlay\Core\Services;

use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Controllers;
use Illuminate\Validation\ValidationException;
use Validator;
use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;

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
            try {

                $transport = (new Swift_SmtpTransport(env('MAIL_HOST'), env('MAIL_PORT')))
                            ->setUsername(env('MAIL_USERNAME'))
                            ->setEncryption(env('MAIL_ENCRYPTION'))
                            ->setPassword(env('MAIL_PASSWORD'));

                // Create the Mailer using your created Transport
                $mailer = new Swift_Mailer($transport);

                // Create a message
                $message = (new Swift_Message('Wonderful Subject'))
                ->setFrom(['john@doe.com' => 'John Doe'])
                ->setTo(['receiver@yopmail.com'])
                ->setBody('Here is the message itself');

                // Send the message
                $result = $mailer->send($message);
             
                echo 'Email has been sent.';
            } catch(Exception $e) {
                echo $e->getMessage();
            }
            
           // $otp = (new Otp())->generate($this->email, $this->device_id);
            if (!$otp->hasErrors()) {
                return $otp->send();
            }
        }
    }
}
