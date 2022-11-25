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
    private array $mimeTypes = [
        'video/x-flv',          // Flash            .flv
        'video/mp4',            // MPEG-4           .mp4
        'application/x-mpegURL',// iPhone Segment   .ts
        'video/3gpp',           // 3GP Mobile       .3gp
        'video/quicktime',      // QuickTime        .mov
        'video/x-msvideo',      // A/V Interleave   .avi
        'video/x-ms-wmv',       // Windows Media    .wmv
    ];
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
        if (!in_array(Storage::disk('upload')->mimeType($this->file), $this->mimeTypes)) {
            throw ValidationException::withMessages(['file' => 'Uploaded file must be a standard video file.']);
        }
    }
}
