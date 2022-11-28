<?php

namespace Aparlay\Core\Api\V1\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UploadedFileIsMedia implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $mimeType = Storage::disk('upload')->mimeType($value);

        return str_starts_with($mimeType, 'video/') || str_starts_with($mimeType, 'image/');
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
