<?php

namespace Aparlay\Core\Api\V1\Services;

use Aparlay\Core\Api\V1\Models\Email;
use Aparlay\Core\Api\V1\Models\Otp;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Repositories\EmailRepository;
use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Jobs\Email as EmailJob;
use Aparlay\Core\Models\Enums\EmailStatus;
use Aparlay\Core\Models\Enums\EmailType;
use Aparlay\Core\Models\Enums\OtpType;
use App\Exceptions\BlockedException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use MongoDB\BSON\ObjectId;

class OtpService
{
    public function __construct()
    {
    }

    /**
     * send otp if status in pending and request otp is null.
     * @param User|Authenticatable $user
     * @param string $deviceId
     * @return bool
     * @throws BlockedException
     */
    public function sendOtp(User|Authenticatable $user, string $deviceId)
    {
        $otp = $this->generateOtp($user->email, $deviceId);
        $this->sendByEmail($user, $otp);

        return true;
    }

    /**
     * Generate OTP.
     * @param string $identity
     * @param string|null $device_id
     * @return \Aparlay\Core\Models\Otp
     * @throws BlockedException
     */
    public function generateOtp(string $identity, string $device_id = null)
    {
        $lastMinute = DT::timestampToUtc(now()->subMinutes(5)->timestamp);

        $previousOTP = Otp::query()->identity($identity)->whereDate('created_at', '>=', $lastMinute)->get();

        $minOtpCreatedAt = $previousOTP->pluck('created_at')->min();

        if (count($previousOTP) > 4) {
            throw new BlockedException(
                __('You cannot create more OTP, please wait :count seconds to request a new otp or try again later.', [
                    'count' => $minOtpCreatedAt->addMinutes(5)->diffInSeconds(now()),
                ]),
                'ERROR',
                'LOCKED',
                Response::HTTP_LOCKED
            );
        }

        // Expire all the Previous OTPs of the given user
        if (count($previousOTP) > 0) {
            foreach ($previousOTP as $model) {
                if (strpos($model->otp, 'expired_') === false) {
                    $model->otp = 'expired_'.random_int(
                        config('app.otp.length.min'),
                        config('app.otp.length.max')
                    );
                    $model->save();
                }
            }
        }

        /** Prepare request params for new OTP request */
        return Otp::create([
            'identity'      => $identity,
            'otp'           => (string) random_int(
                config('app.otp.length.min'),
                config('app.otp.length.max')
            ),
            'expired_at'    => DT::utcDateTime(['s' => config('app.otp.duration')]),
            'type'          => Str::contains($identity, '@') ? OtpType::EMAIL->value : OtpType::SMS->value,
            'device_id'     => $device_id,
            'incorrect'     => 0,
            'validated'     => false,
        ]);
    }

    /**
     * Send OTP by email.
     *
     * @param  User|Authenticatable  $user
     * @param  object                $otp
     *
     * @return bool
     * @throws \Exception
     */
    public function sendByEmail(User|Authenticatable $user, object $otp)
    {
        /* @var Email $lastSentEmail */
        $lastSentEmail = Email::query()->to($otp->identity)->processed()->recentFirst()->first();
        if ($lastSentEmail != null && $lastSentEmail->status === EmailStatus::BOUNCED->value) {
            $otp->delete();
            throw ValidationException::withMessages([
                'email' => $lastSentEmail->humanized_error,
            ]);
        }

        /** Prepare email request data and insert in Email table */
        $request = [
            'to' => $otp->identity,
            'user' => [
                '_id' => new ObjectId($user->_id),
                'username' => $user->username,
                'avatar' => $user->avatar,
            ],
            'status' => EmailStatus::QUEUED->value,
            'type' => EmailType::OTP->value,
        ];

        $email = EmailRepository::create($request);

        /** Prepare email content and dispatch the job to schedule the email */
        $to = $otp->identity;
        $subject = __(':code is your :app verification code', [
            'code' => $otp->otp,
            'app' => config('app.name'),
        ]);
        $type = Email::TEMPLATE_EMAIL_VERIFICATION;
        $payload = [
            'otp' => $otp->otp,
            'otpLink' => '',
            'tracking_url' => config('app.frontend_url').'/t/'.$otp->_id,
        ];
        EmailJob::dispatch((string) $email->_id, $to, $subject, $type, $payload);

        return true;
    }

    /**
     * @param  string  $otp
     * @param  string  $identity
     * @param  bool  $validateOnly
     * @param  bool  $checkValidated
     * @return bool
     */
    public function validateOtp(string $otp, string $identity, bool $validateOnly = false, bool $checkValidated = false): bool
    {
        // Validate the otp for the given user
        $limit = config('app.otp.invalid_attempt_limit');
        $limit--;
        $model = Otp::query()->identity($identity)->otp($otp)->validated($checkValidated)->remainingAttempt($limit)->first();

        if ($model) {
            if ($validateOnly) {
                $model->update(['validated' => true]);
            } else {
                $model->delete();
            }

            return true;
        }
        // Increment the incorrect otp attempt by 1 then through the error
        Otp::query()->identity($identity)->recentFirst()->firstOrFail()->increment('incorrect', 1);

        $previousOTP = Otp::query()->identity($identity)->recentFirst()->first();

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
