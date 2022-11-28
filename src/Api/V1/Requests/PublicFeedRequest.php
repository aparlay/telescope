<?php

namespace Aparlay\Core\Api\V1\Requests;

use Aparlay\Core\Helpers\Country;
use Aparlay\Core\Models\Enums\MediaContentGender;
use Aparlay\Core\Models\Enums\UserSettingShowAdultContent;

use function Aws\map;

use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * @property string $uuid
 * @property int    $show_adult_content
 * @property array  $content_gender
 * @property string $password
 * @property string $gender
 */
class PublicFeedRequest extends FormRequest
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
            'show_adult_content' => ['nullable', Rule::in(UserSettingShowAdultContent::getAllValues())],
            'content_gender.*' => ['integer', Rule::in(MediaContentGender::getAllValues())],
            'payout_country_alpha2' => [Rule::in(array_keys(Country::getAlpha2AndNames()))],
        ];
    }

    /**
     * This function is responsible to perform pre-validation tasks.
     *
     * @throws Exception
     */
    public function prepareForValidation()
    {
        $contentGenders = [];
        foreach (explode(',', request()->input('content_gender', 'female,male,transgender')) as $item) {
            if (! is_numeric($item)) {
                $contentGenders[] = match ($item) {
                    MediaContentGender::FEMALE->label() => MediaContentGender::FEMALE->value,
                    MediaContentGender::MALE->label() => MediaContentGender::MALE->value,
                    MediaContentGender::TRANSGENDER->label() => MediaContentGender::TRANSGENDER->value,
                };
            } else {
                $contentGenders[] = (int) $item;
            }
        }

        $showAdultContent = request()->input('show_adult_content', null);
        $showAdultContent = match ($showAdultContent) {
            UserSettingShowAdultContent::NO->value => UserSettingShowAdultContent::NO->value,
            UserSettingShowAdultContent::ASK->value => UserSettingShowAdultContent::ASK->value,
            UserSettingShowAdultContent::ALWAYS->value => UserSettingShowAdultContent::ALWAYS->value,
            UserSettingShowAdultContent::NO->label() => UserSettingShowAdultContent::NO->value,
            UserSettingShowAdultContent::ASK->label() => UserSettingShowAdultContent::ASK->value,
            UserSettingShowAdultContent::ALWAYS->label() => UserSettingShowAdultContent::ALWAYS->value,
            default => null
        };
        $showAdultContent = $showAdultContent ?? (auth()->guest() ? auth()->user()->setting['show_adult_content'] : 1);

        $this->merge([
            'uuid' => request()->cookie('__Secure_uuid', request()->header('X-DEVICE-ID', '')),
            'is_first_page' => (request()->integer('page') === 0),
            'show_adult_content' => $showAdultContent,
            'content_gender' => $contentGenders,
        ]);
    }
}
