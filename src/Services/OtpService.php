<?php

namespace Aparlay\Core\Services;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Jobs\Email as EmailJob;
use Aparlay\Core\Models\Login;
use Aparlay\Core\Models\Otp;
use Aparlay\Core\Models\Email;
use Aparlay\Core\Services\UserService;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OtpService
{
    /**
     * send otp if status in pending and request otp is null
     * @param User $user
     * @param string $loginEntity
     * @param string $deviceId
     */
    public static function sendOtp(User $user, string $loginEntity, string $deviceId)
    {
        if ($loginEntity === Login::IDENTITY_PHONE_NUMBER) {
            $notificationType = Otp::TYPE_SMS;
            return [
                'message'     => 'If you enter your phone number correctly you will receive an OTP sms soon.',
                'sms_numbers' => $user['phone_number'],
            ];
        } elseif ($loginEntity === Login::IDENTITY_EMAIL) {
            $notificationType = Otp::TYPE_EMAIL;
            
            if ($otp = OtpService::generateOtp($user->email, $deviceId)) {
                if (OtpService::sendByEmail($otp)) {
                    http_response_code(418);
                    // $this->response->statusCode = 418;
                    return [
                        'message' => 'If you enter your email correctly you will receive an OTP email in your inbox soon.',
                    ];
                }
            }
        }
    }

    /**
     * Generate OTP
     * @param string $identity
     * @param string $device_id
     * @return $this|int|string
     * @throws ServerErrorHttpException
     * @throws UnprocessableEntityHttpException|Exception
     * @throws \Exception
     */
    public static function generateOtp(string $identity, string $device_id = null)
    {
        $previousOTP = Otp::where(['identity' => $identity])->get();
        
        if (count($previousOTP) > 4) {
            throw ValidationException::withMessages(['Account' => ['You cannot create more OTP, please wait
             a while to receive an otp or try again later.']]);
            // throw new ErrorException('You cannot create more OTP, please wait a while to receive an otp or try
            //  again later.', Response::HTTP_LOCKED);
        }

        if (count($previousOTP) > 0) {
            foreach ($previousOTP as $model) {
                if (strpos('expired_', $model->otp) === false) {
                    // we do not delete them to can understand limit of 4 otp
                    $model->otp = 'expired_' . random_int(
                        config('app.otp.length.min'),
                        config('app.otp.length.max')
                    );
                    $model->save();
                }
            }
        }

        $otp = new Otp();
        $otp->otp = (string)random_int(
            config('app.otp.length.min'),
            config('app.otp.length.max')
        );
        $otp->expired_at = DT::utcDateTime(['s' => config('app.otp.duration')]);
        $otp->identity = $identity;
        // $otp->type = strpos($identity, '@') ? Otp::TYPE_EMAIL : Otp::TYPE_SMS;
        $otp->type = Str::contains($identity, '@') ? Otp::TYPE_EMAIL : Otp::TYPE_SMS;
        $otp->device_id = $device_id;
        $otp->incorrect = 0;

        if (!$otp->save()) {
            throw ValidationException::withMessages(['Account' => ['Failed to create the object for unknown reason.']]);
            // throw new ErrorException('Failed to create the object for unknown reason.', Response::HTTP_BAD_REQUEST);
        }

        // need to check
        // if (($user = UserService::findByProvidedIdentity($identity)) !== null) {
        //     //$user->gcFailedOtpSequence();
        // }

        return $otp->toArray();
    }


    /**
     * Send OTP by email
     * @param array $otp
     */
    public static function sendByEmail(array $otp)
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
        
        $subject = $otp['otp'] . ' is your Waptap verification code';
        if (new EmailJob($otp['identity'], $subject, $params)) {
            return true;
        }
    }

    /**
     * @param string $otp
     * @param string $identity
     * @param bool $validateOnly
     * @param bool $checkValidated
     * @return bool
     * @throws \yii\db\StaleObjectException
     */
    public static function validateOtp(string $otp, string $identity, bool $validateOnly = false, bool $checkValidated = false)
    {
        $model = Otp::where(['identity' => $identity, 'otp' => $otp, 'incorrect' => ['$in' => [0,1,2]]])->first();
        //, 'validated' => $checkValidated
        if ($model !== null) {
            if ($validateOnly) {
                $model->validated = true;
                $model->save();
            } else {
                $model->delete();
            }
            return true;
        }

        // increment incorrect otp value by 1
        Otp::where('identity', '=', $identity)->increment('incorrect', 1);
        return false;
    }
}
