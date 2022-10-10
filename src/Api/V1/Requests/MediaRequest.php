<?php

namespace Aparlay\Core\Api\V1\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string email
 * @property string phone_number
 * @property string username
 * @property string password
 * @property string gender
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
}
