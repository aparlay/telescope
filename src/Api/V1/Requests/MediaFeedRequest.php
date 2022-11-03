<?php

namespace Aparlay\Core\Api\V1\Requests;

use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Models\Enums\UserDocumentType;
use Aparlay\Core\Models\Enums\UserInterestedIn;
use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * @property string $type
 * @property string $gender_preferences
 */
class MediaFeedRequest extends FormRequest
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
            'type' => ['nullable', 'string', 'max:100'],
            'gender_preferences' => [Rule::in(UserInterestedIn::getAllValues())],
        ];
    }

    /**
     * @return void
     */
    public function prepareForValidation()
    {
        if (empty($this->gender_preferences)) {
            $this->gender_preferences = '';
        }

        $genderPreferences = explode(',', $this->gender_preferences);
        $this->gender_preferences = collect($genderPreferences)
            ->filter(function ($value, $key) {
                return in_array($value, UserInterestedIn::getAllCases());
            })
            ->map(function ($value, $key) {
                return array_search($value, UserInterestedIn::getAllCases());
            })->toArray();
    }
}
