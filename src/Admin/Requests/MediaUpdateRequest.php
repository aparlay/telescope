<?php

namespace Aparlay\Core\Admin\Requests;

use Aparlay\Core\Admin\Models\Media;
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
            'description' => ['nullable', 'string'],
            'is_protected' => ['required', 'boolean'],
            'is_music_licensed' => ['required', 'boolean'],
            'is_comments_enabled' => ['required', 'boolean'],
            'force_sort_positions.guest' => [
                'nullable',
                'integer',
                function ($attribute, $value, $fail) {
                    if ((int) $this->force_sort_positions['guest'] > 0) {
                        $isOtherMedia = Media::where('_id', '!=', $this->media_id)
                            ->where(
                                'force_sort_positions.guest',
                                (int) $this->force_sort_positions['guest']
                            )
                            ->first();
                        if ($isOtherMedia) {
                            $fail('You already set a video at position '.$value.' for the guest.');
                        }
                    }
                },
            ],
            'force_sort_positions.returned' => [
                'nullable',
                'integer',
                function ($attribute, $value, $fail) {
                    if ((int) $this->force_sort_positions['returned'] > 0) {
                        $isOtherMedia = Media::where('_id', '!=', $this->media_id)
                            ->where(
                                'force_sort_positions.returned',
                                (int) $this->force_sort_positions['returned']
                            )
                            ->first();
                        if ($isOtherMedia) {
                            $fail('You already set a video at position '.$value.' for the returned.');
                        }
                    }
                },
            ],
            'force_sort_positions.registered' => [
                'nullable',
                'integer',
                function ($attribute, $value, $fail) {
                    if ((int) $this->force_sort_positions['registered'] > 0) {
                        $isOtherMedia = Media::where('_id', '!=', $this->media_id)
                            ->where(
                                'force_sort_positions.registered',
                                (int) $this->force_sort_positions['registered']
                            )
                            ->first();
                        if ($isOtherMedia) {
                            $fail('You already set a video at position '.$value.' for the registered.');
                        }
                    }
                },
            ],
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
        foreach (['guest', 'returned', 'registered'] as $category) {
            if (request()->integer('force_sort_positions.'.$category) > 0) {
                $this->merge([
                    'force_sort_positions.'.$category => request()->integer('force_sort_positions.'.$category),
                ]);
            } else {
                $this->merge([
                    'force_sort_positions.'.$category => null,
                ]);
            }
        }
        $this->merge([
            'media_id' => new ObjectId($this->media_id),
            'is_protected' => request()->boolean('is_protected'),
            'is_music_licensed' => request()->boolean('is_music_licensed'),
        ]);
    }
}
