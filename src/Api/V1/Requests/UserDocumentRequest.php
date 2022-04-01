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
        $rules = [
            'file' => ['required', 'file'],
            'type' => [
                'required',
                Rule::in([UserDocumentType::ID_CARD->value, UserDocumentType::SELFIE->value]),
            ],
        ];

        if ((int) $this->type === UserDocumentType::SELFIE->value) {
            $rules['file'] = [
               'required',
               'mimes:mp4,mov,ogg,qt',
               'max:50000',
           ];
        }

        if ((int) $this->type === UserDocumentType::ID_CARD->value) {
            $rules['file'] = [
                'required',
                'mimes:jpeg,jpg,png',
                'max:5120',
            ];
        }

        return  $rules;
    }
}
