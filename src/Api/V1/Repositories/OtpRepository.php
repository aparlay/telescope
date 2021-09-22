<?php

namespace Aparlay\Core\Api\V1\Repositories;

use Aparlay\Core\Api\V1\Models\Otp;
use Aparlay\Core\Helpers\DT;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class OtpRepository
{
    /**
     * Responsible to create OTP.
     * @param  array  $otp
     * @return Otp
     * @throws \Exception
     */
    public static function create(array $otp)
    {
        /* Set the Default Values and required to be input parameters */
        try {
            return Otp::create([
                'identity'      => $otp['identity'],
                'otp'           => (string) random_int(
                    config('app.otp.length.min'),
                    config('app.otp.length.max')
                ),
                'expired_at'    => DT::utcDateTime(['s' => config('app.otp.duration')]),
                'type'          => Str::contains($otp['identity'], '@') ? Otp::TYPE_EMAIL : Otp::TYPE_SMS,
                'device_id'     => $otp['device_id'],
                'incorrect'     => 0,
                'validated'     => false,
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return null;
        }
    }

    /**
     * Expire the previous OTPs.
     * @param  object  $otps
     * @return bool|void
     * @throws \Exception
     */
    public static function expire(object $otps)
    {
        if (count($otps) > 0) {
            foreach ($otps as $model) {
                if (strpos($model->otp, 'expired_') === false) {
                    $model->otp = 'expired_'.random_int(
                        config('app.otp.length.min'),
                        config('app.otp.length.max')
                    );
                    $model->save();
                }
            }
        }
    }

    public function __construct($model)
    {
    }

    public function all()
    {
        // TODO: Implement all() method.
    }

    public function update(array $data, $id)
    {
        // TODO: Implement update() method.
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
    }

    public function find($id)
    {
        // TODO: Implement find() method.
    }
}
