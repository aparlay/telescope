<?php

namespace Aparlay\Core\Admin\Requests;

use Aparlay\Core\Admin\Models\Media;
use Aparlay\Core\Models\Enums\MediaContentGender;
use Aparlay\Core\Models\Enums\MediaStatus;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MediaUpdateScoreRequest extends FormRequest
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
                'required',
                'integer',
                Rule::in(array_values(Media::getSkinScores())),
            ],
            'awesomeness_score' => [
                'required',
                'integer',
                Rule::in(array_values(Media::getAwesomenessScores())),
            ],
            'beauty_score' => [
                'required',
                'integer',
                Rule::in(array_values(Media::getBeautyScores())),
            ],
            'status' => [
                'required',
                'integer',
                Rule::in(array_keys(Media::getStatuses())),
            ],
            'content_gender' => [
                'required',
                'integer',
                Rule::in(MediaContentGender::getAllValues()),
            ],
        ];
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

    public function prepareForValidation()
    {
        if (
            (int) $this->status === MediaStatus::CONFIRMED->value &&
            (
                empty($this->skin_score) ||
                empty($this->awesomeness_score) ||
                empty($this->beauty_score) ||
                ! isset($this->content_gender)
            )
        ) {
            throw ValidationException::withMessages([
                'avatar' => 'You must set all scores and content gender before confirm a video for public feed.',
            ]);
        }
    }
}
