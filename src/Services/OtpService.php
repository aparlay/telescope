<?php

namespace Aparlay\Core\Services;

use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Jobs\Email as EmailJob;
use Aparlay\Core\Models\Email;
use Aparlay\Core\Models\Otp;
use Aparlay\Core\Repositories\EmailRepository;
use Aparlay\Core\Repositories\OtpRepository;
use App\Exceptions\BlockedException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class OtpService
{
    /**
     * send otp if status in pending and request otp is null.
     * @param  User  $user
     * @param  string  $deviceId
     * @return bool
     * @throws BlockedException
     */
    public static function sendOtp(User | Authenticatable $user, string $deviceId)
    {
        $otp = self::generateOtp($user->email, $deviceId);
        self::sendByEmail($user, $otp);

        return true;
    }

    /**
     * Generate OTP.
     * @param string $identity
     * @param string|null $device_id
     * @return \Aparlay\Core\Api\V1\Models\Otp
     * @throws BlockedException
     */
    public static function generateOtp(string $identity, string $device_id = null)
    {
        $previousOTP = Otp::identity($identity)->get();

        if (count($previousOTP) > 4) {
            throw new BlockedException(
                'You cannot create more OTP, please wait a while to receive an otp or try again later.',
                null,
                null,
                Response::HTTP_LOCKED
            );
        }

        // Expire all the Previous OTPs of the given user
        OtpRepository::expire($previousOTP);

        /** Prepare request params for new OTP request */
        $request = [
            'identity'      => $identity,
            'device_id'     => $device_id,
        ];

        return OtpRepository::create($request);
    }

    /**
     * Send OTP by email.
     * @param User $user
     * @param object $otp
     * @return bool
     */
    public static function sendByEmail(User | Authenticatable $user, object $otp)
    {
        /** Prepare email request data and insert in Email table */
        $request = [
            'to' => $otp->identity,
            'user' => $user->toArray(),
        ];

        EmailRepository::create($request);

        /** Prepare email content and dispatch the job to schedule the email */
        $content = [
            'subject'               => $otp->otp.' is your verification code',
            'identity'              => $otp->identity,
            'email_template_params' => [
                'otp'               => $otp->otp,
                'otpLink'           => '',
                'tracking_url'      => config('app.frontendUrl').'/t/'.$otp->_id,
            ],
            'email_type'            => Email::TEMPLATE_EMAIL_VERIFICATION,
        ];
        EmailJob::dispatch($content);

        return true;
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
        $model = Otp::identity($identity)
            ->otp($otp)
            ->validated($checkValidated)
            ->remainingAttempt($limit)
            ->first();
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
        Otp::identity($identity)
            ->RecentFirst()
            ->first()
            ->increment('incorrect', 1);

        throw ValidationException::withMessages([
            'otp' => ['Incorrect otp.'],
        ]);
    }
}
