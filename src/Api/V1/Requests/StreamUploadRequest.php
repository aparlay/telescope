<?php

namespace Aparlay\Core\Api\V1\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string email
 * @property string gender
 * @property string password
 * @property string phone_number
 * @property string username
 */
class StreamUploadRequest extends FormRequest
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
            'description' => ['required', 'string'],
            'visibility' => ['nullable', 'integer'],
            'file' => ['required', 'file'],
        ];
    }
}
