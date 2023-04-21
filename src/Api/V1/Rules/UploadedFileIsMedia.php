<?php

namespace Aparlay\Core\Api\V1\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class UploadedFileIsMedia implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $mimeType = Storage::disk('upload')->mimeType($value);

        return in_array($mimeType, [
            'video/x-msvideo',
            'video/quicktime',
            'video/x-m4v',
            'video/mp4',
            'video/webm',
            'video/mpeg',
            'video/ogg',
            'video/3gpp',
            'video/3gpp2',

            'image/jpeg',
            'image/png',
            'image/webp',
        ]);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('Uploaded :attribute must be a standard video or image file.');
    }
}
