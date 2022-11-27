<?php

namespace Aparlay\Core\Api\V1\Requests;

use Aparlay\Core\Api\V1\Rules\UploadedFileExists;
use Aparlay\Core\Api\V1\Rules\UploadedFileIsVideo;
use Illuminate\Foundation\Http\FormRequest;

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
            'visibility' => ['nullable', 'integer'],
            'file' => ['required', 'string', new UploadedFileExists(), new UploadedFileIsVideo()],
        ];
    }
}
