<?php

namespace Aparlay\Core\Api\V1\Repositories;

use Aparlay\Core\Api\V1\Models\Email;
use Aparlay\Core\Models\Enums\EmailStatus;
use Aparlay\Core\Models\Enums\EmailType;
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
            return Email::create($email);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return null;
        }
    }
}
