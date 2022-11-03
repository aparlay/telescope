<?php

namespace Aparlay\Core\Admin\Requests;

use Aparlay\Core\Admin\Models\Media;
use Aparlay\Core\Models\Enums\UserInterestedIn;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use MongoDB\BSON\ObjectId;

class MediaUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'skin_score' => [
                'nullable',
                'integer',
                Rule::in(array_keys(Media::getSkinScores())),
            ],
            'awesomeness_score' => ['nullable',
                'integer',
                Rule::in(array_keys(Media::getAwesomenessScores())),
            ],
            'beauty_score' => ['nullable',
                'integer',
                Rule::in(array_keys(Media::getBeautyScores())),
            ],
            'content_gender' => ['nullable', 'array'],
            'content_gender.*' => [Rule::in(UserInterestedIn::getAllValues())],
            'description' => ['nullable', 'string'],
            'metadata' => ['nullable', 'string'],
            'status' => [
                'nullable',
                Rule::in(array_keys(Media::getStatuses())),
            ],
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'content_gender' => array_map('intval', $this->content_gender),
        ]);
    }

    /**
     * @param Validator $validator
     * @return void
     */
    public function failedValidation(Validator $validator)
    {
        $errors = $validator->errors(); // Here is your array of errors

        throw new HttpResponseException(
            redirect()->back()->withErrors($errors)
        );
    }
}
