<?php

namespace Aparlay\Core\Api\V1\Requests;

use Aparlay\Core\Helpers\Country;
use Aparlay\Core\Models\Enums\MediaContentGender;
use Aparlay\Core\Models\Enums\UserSettingShowAdultContent;
use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string $uuid
 * @property int    $show_adult_content
 * @property array  $content_gender
 * @property string $password
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
            UserSettingShowAdultContent::NEVER->value => UserSettingShowAdultContent::NEVER->value,
            UserSettingShowAdultContent::ASK->value => UserSettingShowAdultContent::ASK->value,
            UserSettingShowAdultContent::TOPLESS->value => UserSettingShowAdultContent::TOPLESS->value,
            UserSettingShowAdultContent::ALL->value => UserSettingShowAdultContent::ALL->value,
            UserSettingShowAdultContent::NEVER->label() => UserSettingShowAdultContent::NEVER->value,
            UserSettingShowAdultContent::ASK->label() => UserSettingShowAdultContent::ASK->value,
            UserSettingShowAdultContent::TOPLESS->label() => UserSettingShowAdultContent::TOPLESS->value,
            UserSettingShowAdultContent::ALL->label() => UserSettingShowAdultContent::ALL->value,
            default => null
        };
        $showAdultContent = $showAdultContent ?? (auth()->guest() ? 1 : auth()->user()->setting['show_adult_content']);

        $this->merge([
            'uuid' => request()->cookie('__Secure_uuid', request()->header('X-DEVICE-ID', '')),
            'is_first_page' => (request()->integer('page') === 0),
            'show_adult_content' => $showAdultContent,
            'content_gender' => $contentGenders,
        ]);
    }
}
