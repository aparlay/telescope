<?php

namespace Aparlay\Core\Api\V1\Traits;

use Illuminate\Validation\ValidationException;

trait ValidationErrorTrait
{
    /**
     * @param string $field
     * @param string $error
     * @return mixed
     * @throws ValidationException
     */
    public function throwClientError(string $field, string $error)
    {
        throw ValidationException::withMessages([
            $field => $error,
        ]);
    }
}
