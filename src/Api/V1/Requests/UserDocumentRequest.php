<?php

namespace Aparlay\Core\Api\V1\Requests;

use Aparlay\Core\Models\Enums\UserDocumentType;
use Illuminate\Validation\Rule;

class UserDocumentRequest extends BaseFormRequest
{
    const ONE_MB = 1024;
    private $maxInMb = 20;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'file' => [
                'required',
                'mimes:jpeg,jpg,png',
                'max:'.self::ONE_MB * 20,
            ],
            'type' => [
                'required',
                Rule::in([UserDocumentType::ID_CARD->value, UserDocumentType::SELFIE->value]),
            ],
        ];

        return  $rules;
    }

    public function messages()
    {
        return [
            'file.max' => __('Maximum file size to upload is :max MB, try to reduce its resolution to make it under :max MB', [
                'max' => $this->maxInMb,
            ]),
        ];
    }
}
