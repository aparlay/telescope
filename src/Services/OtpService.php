<?php

namespace Aparlay\Core\Services;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Jobs\Email as EmailJob;
use Aparlay\Core\Models\Otp;
use Aparlay\Core\Models\User;
use Aparlay\Core\Models\Email;
use Illuminate\Http\Response;

class OtpService
{   
    /**
     * @param $identity
     * @param $device_id
     * @param $validated
     * @return $this|int|string
     * @throws ServerErrorHttpException
     * @throws UnprocessableEntityHttpException|Exception
     * @throws \Exception
     */
    public static function generate($identity, $device_id = null)
    {
        $previousOTP = Otp::where(['identity' => $identity])->get();
        if (count($previousOTP) > 4) {
            return $this->error('You cannot create more OTP, please wait a while to receive an otp or try again later.', [], Response::HTTP_UNAUTHORIZED);
            // throw new LockedHttpException(V1::t('app',
            //     'You cannot create more OTP, please wait a while to receive an otp or try again later.'));
        }

        if (count($previousOTP) > 0) {
            foreach ($previousOTP as $model) {
                if (strpos('expired_', $model->otp) === false) {
                    // we do not delete them to can understand limit of 4 otp
                    $model->otp = 'expired_' . random_int('1000', '9999');
                    $model->save();

                }
            }
        }

        $otp = new Otp();
        $otp->otp = (string)random_int('1000', '9999');
        $otp->expired_at = DT::utcDateTime(['s' => 600]);
        $otp->identity = $identity;
        $otp->type = strpos($identity, '@') ? Otp::TYPE_EMAIL : Otp::TYPE_SMS;
        $otp->device_id = $device_id;

        if (!$otp->save()) {
            return $this->error('Failed to create the object for unknown reason.', [], Response::HTTP_BAD_REQUEST);
        }

        // need to check
        // if (($user = User::findByProvidedIdentity($identity)) !== null) {
        //     $user->gcFailedOtpSequence();
        // }

        return $otp->toArray();
    }

    /**
     *
     */
    public static function send($otp)
    {   
        if ($otp['type'] === Otp::TYPE_EMAIL) {
            OtpService::sendByEmail($otp);
        } else {
            $this->sendBySMS($otp);
        }

        return true;
    }

    /**
     * Send OTP by email
     */
    public static function sendByEmail($otp)
    {
        $attributes = [
            'to' => $otp['identity'],
            'user' => [
                '_id' => null,
                'username' => '',
                'avatar' => '',
            ],
            'status' => Email::STATUS_QUEUED,
            'type' => Email::TYPE_OTP,
        ];
        
        if (($user = User::email($otp['identity'])->first()) !== null) {
            $attributes['user'] = [
                '_id' => $user->_id,
                'username' => $user->username,
                'avatar' => $user->avatar,
            ];
        }

        $model = new Email();
        $model->attributes = $attributes;
        $model->save();

        $params = [
            'otp' => $otp['otp'],
            'otpLink' => '',//Url::to('@frontend/images/logo.gif', 'https'),
            'tracking_url' => config('app.frontendUrl') . '/t/' . $model->_id,
        ];

        //Send the message
        $tracking_id = $model->_id;
        $EmailSubject = $otp['otp'].' is your Waptap verification code';
        $to = $otp['identity'];
        $SendEmailBody = $otp['otp'].' is your Waptap verification code';
        $mail = new EmailJob($EmailSubject, $to, $SendEmailBody);
    }

    /**
     * Send OTP by SMS
     */
    private function sendBySMS()
    {
        $message = Yii::t('app', 'Your verification code is:\n{code}', ['code' => $this->otp]);
        Yii::$app->queue->priority(1)->push(new SmsJob([
            'phoneNumber' => $this->identity,
            'message' => $message,
        ]));
    }

}