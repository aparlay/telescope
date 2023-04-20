<?php

namespace Aparlay\Core\Admin\Requests;

use Aparlay\Core\Admin\Models\Media;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

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
            'description' => ['nullable', 'string'],
            'is_protected' => ['required', 'boolean'],
            'is_music_licensed' => ['required', 'boolean'],
            'is_comments_enabled' => ['required', 'boolean'],
            'status' => [
                'required',
                'integer',
                Rule::in(array_keys(Media::getStatuses())),
            ],
        ];
    }

    /**
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
        $this->merge([
            'is_protected' => request()->boolean('is_protected'),
            'is_music_licensed' => request()->boolean('is_music_licensed'),
            'is_comments_enabled' => request()->boolean('is_comments_enabled'),
        ]);
    }
}
