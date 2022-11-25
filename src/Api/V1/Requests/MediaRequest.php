<?php

namespace Aparlay\Core\Api\V1\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;
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
            'visibility' => ['nullable', 'integer'],
            'file' => ['required', 'string'],
        ];
    }

    /**
     * @return void
     */
    public function prepareForValidation()
    {
        if (!Storage::disk('upload')->exists($this->file)) {
            throw ValidationException::withMessages(['file' => 'Uploaded file does not exists.']);
        }
        if (!str_contains(Storage::disk('upload')->mimeType($this->file), 'video/')) {
            throw ValidationException::withMessages(['file' => 'Uploaded file must be a standard video file.']);
        }
    }
}
