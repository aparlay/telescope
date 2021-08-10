<?php

namespace Aparlay\Core\Services;

use Aparlay\Core\Api\V1\Requests\EmailRequest;
use Aparlay\Core\Api\V1\Requests\OtpRequest;
use Aparlay\Core\Jobs\Email as EmailJob;
use Aparlay\Core\Models\Email;
use Aparlay\Core\Models\Login;
use Aparlay\Core\Models\Otp;
use Aparlay\Core\Models\Scopes\OtpScope;
use Aparlay\Core\Services\EmailService;
use App\Exceptions\BlockedException;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class OtpService
{
    /**
     * send otp if status in pending and request otp is null.
     * @param User $user
     * @param string $loginEntity
     * @param string $deviceId
     * @throws \BlockedException
     */
    public static function sendOtp(User $user, string $loginEntity, string $deviceId)
    {
        if ($loginEntity === Login::IDENTITY_PHONE_NUMBER) {
            throw new BlockedException('OTP has been sent.', null, null, Response::HTTP_LOCKED, [
                'message' => 'If you enter your phone number correctly you will receive an OTP sms soon.',
                'sms_numbers' => $user['phone_number'],
            ]);
        } elseif ($loginEntity === Login::IDENTITY_EMAIL) {
            if ($otp = self::generateOtp($user->email, $deviceId)) {
                if (self::sendByEmail($user, $otp)) {
                    throw new BlockedException('OTP has been sent.', null, null, Response::HTTP_LOCKED, [
                        'message' => 'If you enter your email correctly you will receive an OTP email in your inbox soon.',
                    ]);
                }
            }
        }
    }

    /**
     * Generate OTP.
     * @param string $identity
     * @param string $device_id
     * @return array
     * @throws \BlockedException
     */
    public static function generateOtp(string $identity, string $device_id = null)
    {
        $previousOTP = Otp::FilterByIdentity($identity)->get();

        if (count($previousOTP) > 4) {
            throw new BlockedException(
                'You cannot create more OTP, please wait a while to receive an otp or try again later.',
                null,
                null,
                Response::HTTP_LOCKED
            );
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
     * Expire the previous OTPs.
     * @param object $otps
     * @return bool|void
     */
    public static function expireOtp(object $otps)
    {
        if (count($otps) > 0) {
            foreach ($otps as $model) {
                if (strpos($model->otp, 'expired_') === false) {
                    $model->otp = 'expired_'.random_int(
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
     * Create OTP.
     * @param OtpRequest $request
     */
    public static function create(OtpRequest $request)
    {
        $request->prepareForValidation();

        return Otp::create($request->all());
    }

    /**
     * Send OTP by email.
     * @param User $user
     * @param object $otp
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
            'subject'               => $otp->otp.' is your verification code',
            'identity'              => $otp->identity,
            'email_template_params' => [
                'otp'               => $otp->otp,
                'otpLink'           => '',
                'tracking_url'      => config('app.frontendUrl').'/t/'.$otp->_id,
            ],
            'email_type'            => 'email_verification',
        ];

        if (new EmailJob($content)) {
            return true;
        }
    }

    /**
     * @param string $otp
     * @param string $identity
     * @return bool
     * @throws ValidationException
     */
    public static function validateOtp(string $otp, string $identity, bool $validateOnly = false, bool $checkValidated = false)
    {
        // Validate the otp for the given user
        $limit = config('app.otp.invalid_attempt_limit');
        $limit--;
        $model = Otp::OtpIdentity($otp, $identity, $checkValidated, $limit)->first();
        if ($model) {
            if ($validateOnly) {
                $model->validated = true;
                $model->save();
            } else {
                $model->delete();
            }

            return true;
        }
        // Increment the incorrect otp attempt by 1 then through the error
        Otp::OtpIncorrect($identity)->increment('incorrect', 1);

        throw ValidationException::withMessages([
            'otp' => ['Incorrect otp.'],
        ]);
    }
}
