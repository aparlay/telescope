<?php

namespace Aparlay\Core\Api\V1\Services;

use Aparlay\Core\Api\V1\Models\Email;
use Aparlay\Core\Api\V1\Models\Otp;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Repositories\EmailRepository;
use Aparlay\Core\Api\V1\Repositories\OtpRepository;
use Aparlay\Core\Jobs\Email as EmailJob;
use App\Exceptions\BlockedException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class OtpService
{
    protected OtpRepository $otpRepository;

    public function __construct()
    {
        $this->otpRepository = new OtpRepository(new Otp());
    }

    /**
     * send otp if status in pending and request otp is null.
     * @param  User|Authenticatable  $user
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
     * @return \Aparlay\Core\Models\Otp
     * @throws BlockedException
     */
    public static function generateOtp(string $identity, string $device_id = null)
    {
        $previousOTP = Otp::identity($identity)->get();

        if (count($previousOTP) > 4) {
            throw new BlockedException(
                'You cannot create more OTP, please wait a while to receive an otp or try again later.',
                'ERROR',
                'LOCKED',
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
     * @param User|Authenticatable $user
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
        $email = $otp->identity;
        $subject = $otp->otp. __(' is your :app_name: verification code', ['app_name' => config('app.name')]);
        $type = Email::TEMPLATE_EMAIL_VERIFICATION;
        $payload = [
            'otp'               => $otp->otp,
            'otpLink'           => '',
            'tracking_url'      => config('app.frontendUrl').'/t/'.$otp->_id,
        ];
        EmailJob::dispatch($email, $subject, $type, $payload);

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
            ->recentFirst()
            ->first()
            ->increment('incorrect', 1);

        $previousOTP = Otp::identity($identity)
                        ->recentFirst()
                        ->first();

        if ($previousOTP->incorrect > 3) {
            throw ValidationException::withMessages([
                'otp' => ['Too many failed attempts, please try again by requesting new code.'],
            ]);
        } else {
            throw ValidationException::withMessages([
                'otp' => ['Invalid Code.'],
            ]);
        }
    }
}
