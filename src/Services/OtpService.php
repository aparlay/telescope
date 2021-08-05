<?php

namespace Aparlay\Core\Services;

use Aparlay\Core\Api\V1\Requests\OtpRequest;
use Aparlay\Core\Api\V1\Requests\EmailRequest;
use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Jobs\Email as EmailJob;
use Aparlay\Core\Models\Login;
use Aparlay\Core\Models\Otp;
use Aparlay\Core\Models\Email;
use Aparlay\Core\Models\Scopes\OtpScope;
use Aparlay\Core\Services\EmailService;
use Aparlay\Core\Services\UserService;
use App\Exceptions\InvalidOtpException;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

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
            return response([
                'data' => [
                    'message' => 'If you enter your phone number correctly you will receive an OTP sms soon.'],
                    'sms_numbers' => $user['phone_number']
                ], Response::HTTP_OK);
        } elseif ($loginEntity === Login::IDENTITY_EMAIL) {
            if ($otp = self::generateOtp($user->email, $deviceId)) {
                if (self::sendByEmail($user, $otp)) {
                    throw new InvalidOtpException([
                        'data' => [
                            'message' => 'If you enter your email correctly you will receive an OTP email in your inbox soon.'
                        ]], 418);

                        // $data = [
                        //     'message' => 'If you enter your email correctly you will receive an OTP email in your inbox soon.'
                        // ];
                        // throw new InvalidOtpException('OTP has been sent.', null, null , 418, $data);
                }
            }
        }
    }

    /**
     * Generate OTP
     * @param string $identity
     * @param string $device_id
     * @return Array
     * @throws \ValidationException
     */
    public static function generateOtp(string $identity, string $device_id = null)
    {
        $previousOTP = Otp::FilterByIdentity($identity)->get();

        if (count($previousOTP) > 4) {
            throw new InvalidOtpException('You cannot create more OTP, please wait a while to receive an otp or try again later.');
        }

        // Expire all the Previous OTPs of the given user
        self::expireOtp($previousOTP);

        /** Prepare request params for new OTP request */
        $request = [
            'identity'      => $identity,
            'device_id'     => $device_id,
        ];
        $request = new OtpRequest((array) $request);
        $otp = self::create($request);
        
        return $otp;
    }

    /**
     * Expire the previous OTPs
     * @param Object $otps
     * @return Boolean|Void
     */
    public static function expireOtp(object $otps)
    {
        if (count($otps) > 0) {
            foreach ($otps as $model) {
                if (strpos($model->otp, 'expired_') === false) {
                    $model->otp = 'expired_' . random_int(
                        config('app.otp.length.min'),
                        config('app.otp.length.max')
                    );
                    $model->save();
                    return true;
                }
            }
        }
    }

    /**
     * Create OTP
     * @param OtpRequest $request
     */
    public static function create(OtpRequest $request)
    {
        $request->prepareForValidation();
        return Otp::create($request->all());
    }

    /**
     * Send OTP by email
     * @param User $user
     * @param Object $otp
     */
    public static function sendByEmail(User $user, object $otp)
    {
        $request = [
            'to' => $otp->identity,
            'user' => $user->toArray(),
        ];

        $request = new EmailRequest($request);
        EmailService::create($request);

        /** Prepare email content and dispatch the job to schedule the email */
        $content = [
            'subject' => $otp->otp . ' is your verification code',
            'identity' => $otp->identity,
            'email_template_params' => [
                'otp' => $otp->otp,
                'otpLink' => '',
                'tracking_url' => config('app.frontendUrl') . '/t/' . $otp->_id,
            ],
            'email_type' => 'email_verification'
        ];
        
        if (new EmailJob($content)) {
            return true;
        }
    }

    /**
     * @param String $otp
     * @param String $identity
     * @return Boolean
     * @throws ValidationException
     */
    public static function validateOtp(string $otp, string $identity)
    {
        // Validate the otp for the given user
        $limit = config('app.otp.invalid_attempt_limit');
        $model = Otp::OtpIdentity($otp, $identity, $limit)->get();        
        if ($model) {
            self::expireOtp(new Object($model));
            return true;
        }

        // Increment the incorrect otp attempt by 1 then through the error
        // Otp::where('identity', '=', $identity)->increment('incorrect', 1);
        Otp::OtpIncorrect($identity)->increment('incorrect', 1);
        throw ValidationException::withMessages([
            'code' => ['Invalid code.']
        ]);
    }
}
