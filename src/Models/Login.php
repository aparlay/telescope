<?php

namespace Aparlay\Core\Models;

/**
 * Login model
 *
 * @OA\Schema()
 *
 */
class Login extends Model
{
    public const IDENTITY_EMAIL = 'email';
    public const IDENTITY_PHONE_NUMBER = 'phone_number';
    public const IDENTITY_USERNAME = 'username';
}
