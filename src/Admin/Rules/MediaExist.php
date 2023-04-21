<?php

namespace Aparlay\Core\Admin\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class MediaExist implements Rule
{
    public function __construct($disk)
    {
        $this->disk = $disk;
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
        return Storage::disk($this->disk)->exists($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute did not exist in storage.';
    }
}
