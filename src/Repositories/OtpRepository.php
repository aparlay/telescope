<?php

namespace Aparlay\Core\Repositories;

use Aparlay\Core\Models\Otp;

class OtpRepository
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
    public function generate($identity, $device_id = null)
    {
        $previousOTP = Otp::find()->where(['identity' => $identity])->all();
        if (count($previousOTP) > 4) {
            return $this->error('You cannot create more OTP, please wait a while to receive an otp or try again later.', [], Response::HTTP_UNAUTHORIZED);
            throw new LockedHttpException(V1::t('app',
                'You cannot create more OTP, please wait a while to receive an otp or try again later.'));
        }

        foreach ($previousOTP as $model) {
            if (strpos('expired_', $model->otp) === false) {
                // we do not delete them to can understand limit of 4 otp
                $model->otp = 'expired_' . random_int(
                    Yii::$app->params['otp']['length']['min'],
                    Yii::$app->params['otp']['length']['max'],
                );
                $model->save();

            }
        }

        $this->otp = (string)random_int(
            Yii::$app->params['otp']['length']['min'],
            Yii::$app->params['otp']['length']['max'],
        );
        $this->expired_at = DT::utcDateTime(['s' => Yii::$app->params['otp']['duration']]);
        $this->identity = $identity;
        $this->type = strpos($identity, '@') ? self::TYPE_EMAIL : self::TYPE_SMS;
        $this->device_id = $device_id;

        $this->save();

        if ($this->hasErrors()) {
            Yii::error($this->errors);
            throw new UnprocessableEntityHttpException('Failed to create the object for unknown reason.');
        }

        if (($user = User::findByProvidedIdentity($identity)) !== null) {
            $user->gcFailedOtpSequence();
        }

        return $this;
    }

}