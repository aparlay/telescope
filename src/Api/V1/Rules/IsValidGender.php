<?php

namespace Aparlay\Core\Api\V1\Rules;

use Aparlay\Core\Api\V1\Models\User;
use Illuminate\Contracts\Validation\Rule;

class IsValidGender implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return in_array($value, array_keys(User::getGenders()));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Gender MUST be valid.';
    }
}
