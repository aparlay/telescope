<?php

namespace Aparlay\Core\Api\V1\Traits;

use Illuminate\Validation\ValidationException;

trait ValidationErrorTrait
{
    /**
     * @throws ValidationException
     *
     * @return mixed
     */
    public function throwClientError(string $field, string $error)
    {
        throw ValidationException::withMessages([
            $field => $error,
        ]);
    }
}
