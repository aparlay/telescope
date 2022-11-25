<?php

namespace Aparlay\Core\Api\V1\Requests;

use Aparlay\Core\Api\V1\Rules\UploadedFileExists;
use Aparlay\Core\Api\V1\Rules\UploadedFileIsMedia;
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
        'application/x-mpegURL', // iPhone Segment   .ts
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
            'file' => ['required', 'string', new UploadedFileExists(), new UploadedFileIsVideo()],
        ];
    }
}
