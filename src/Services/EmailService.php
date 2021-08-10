<?php

namespace Aparlay\Core\Services;

use Aparlay\Core\Api\V1\Requests\EmailRequest;
use Aparlay\Core\Models\Email;

class EmailService
{
    /**
     * save Email.
     * @param EmailRequest $request
     * @return void
     */
    public static function create(EmailRequest $request)
    {
        $request->prepareForValidation();
        $email = new Email();
        $email->attributes = $request->all();
        $email->save();
    }
}
