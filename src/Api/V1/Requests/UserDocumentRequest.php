<?php

namespace Aparlay\Core\Api\V1\Requests;

use Aparlay\Core\Models\Enums\UserDocumentType;
use Illuminate\Validation\Rule;

class UserDocumentRequest extends BaseFormRequest
{
    const ONE_MB = 1024;

    private $maxInMb;

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
                Rule::in([UserDocumentType::ID_CARD->value, UserDocumentType::VIDEO_SELFIE->value]),
            ],
        ];

        if ((int) $this->type === UserDocumentType::VIDEO_SELFIE->value) {
            $this->maxInMb = 200;
            $rules['file'] = [
               'required',
               'mimes:mp4,mov,ogg,qt',
               'max:'.self::ONE_MB * $this->maxInMb,
           ];
        }

        if ((int) $this->type === UserDocumentType::ID_CARD->value) {
            $this->maxInMb = 20;
            $rules['file'] = [
                'required',
                'mimes:jpeg,jpg,png',
                'max:'.self::ONE_MB * $this->maxInMb,
            ];
        }

        return  $rules;
    }

    public function messages()
    {
        return [
            'file.max' => __('Maximum file size to upload is :max MB If you are uploading a photo or video, try to reduce its resolution to make it under :max MB', [
                'max' => $this->maxInMb,
            ]),
        ];
    }
}
