<?php

namespace Aparlay\Core\Api\V1\Repositories;

use Aparlay\Core\Api\V1\Models\Email;
use Illuminate\Support\Facades\Log;

class EmailRepository
{
    /**
     * Responsible to create email.
     * @param array $email
     * @return Email
     * @throws \Exception
     */
    public static function create(array $email)
    {
        /* Set the Default Values and required to be input parameters */
        try {
            return Email::create([
                'to' => $email['to'],
                'user' => [
                    '_id' => $email['user']['_id'],
                    'username' => $email['user']['username'],
                    'avatar' => $email['user']['avatar'],
                ],
                'status' => Email::STATUS_QUEUED,
                'type' => Email::TYPE_OTP,
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return null;
        }
    }
}
