<?php

namespace Aparlay\Core\Api\V1\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BaseFormRequest extends FormRequest
{
    /**
     * This usually is true so it was moved to BaseFormRequest method.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
