<?php

namespace Aparlay\Core\Repositories;

use Aparlay\Core\Models\Email;

class EmailRepository
{
    /**
     * Responsible to create email.
     * @param array $email
     * @return Email
     */
    public static function create(array $email)
    {
        /* Set the Default Values and required to be input parameters */
        $modal = new Email();
        $attributes = [
            'to' => $email['to'],
            'user' => [
                '_id' => $email['user']['_id'],
                'username' => $email['user']['username'],
                'avatar' => $email['user']['avatar'],
            ],
            'status' => Email::STATUS_QUEUED,
            'type' => Email::TYPE_OTP,
        ];

        $modal->attributes = $attributes;
        $modal->save();

        return $modal;
    }
}
