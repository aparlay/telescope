<?php

namespace Aparlay\Core\Api\V1\Repositories;

use Aparlay\Core\Api\V1\Models\Email;
use Exception;
use Illuminate\Support\Facades\Log;

class EmailRepository
{
    /**
     * Responsible to create email.
     *
     * @throws Exception
     *
     * @return Email
     */
    public static function create(array $email)
    {
        /* Set the Default Values and required to be input parameters */
        try {
            return Email::create($email);
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return;
        }
    }
}
