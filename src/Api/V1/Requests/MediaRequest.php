<?php

namespace Aparlay\Core\Api\V1\Requests;

use Aparlay\Core\Api\V1\Rules\UploadedFileExists;
use Aparlay\Core\Api\V1\Rules\UploadedFileIsVideo;
use Aparlay\Core\Models\Enums\MediaContentGender;
use Aparlay\Core\Models\Enums\MediaVisibility;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * @property string $description
 * @property string $visibility
 * @property string $file
 */
class MediaRequest extends FormRequest
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
            'description' => ['nullable', 'string', 'max:200'],
            'visibility' => ['integer', Rule::in(MediaVisibility::getAllValues())],
            'file' => ['required', 'string', new UploadedFileExists(), new UploadedFileIsVideo()],
        ];
    }

    /**
     * This function is responsible to perform pre-validation.
     */
    public function prepareForValidation()
    {
        $visibility = request()->input('visibility', 'public');
        if (! is_numeric($visibility)) {
            $visibility = match ($visibility) {
                MediaVisibility::PRIVATE->label() => MediaVisibility::PRIVATE->value,
                MediaVisibility::PUBLIC->label() => MediaVisibility::PUBLIC->value,
                default => MediaVisibility::PUBLIC->value,
            };
        } else {
            $visibility = (int) $visibility;
        }

        $this->merge([
            'visibility' => $visibility,
        ]);
    }
}
