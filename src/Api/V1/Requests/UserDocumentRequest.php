<?php

namespace Aparlay\Core\Api\V1\Requests;

use Aparlay\Core\Models\Enums\UserDocumentType;
use Illuminate\Validation\Rule;

class UserDocumentRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'file' => ['required', 'file'],
            'type' => [
                'required',
                Rule::in([UserDocumentType::ID_CARD->value, UserDocumentType::SELFIE->value]),
            ],
        ];
    }
}
