<?php

namespace Aparlay\Core\Api\V1\Requests;

use Aparlay\Core\Models\Enums\UserInterestedIn;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string $type
 * @property array $gender_preferences
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
            'gender_preferences.*' => [Rule::in(UserInterestedIn::getAllValues())],
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
                return in_array($value, UserInterestedIn::getAllValues());
            })
            ->map(function ($value, $key) {
                return (int) $value;
            })->toArray();
    }
}
