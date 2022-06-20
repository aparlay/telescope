<?php

namespace Aparlay\Core\Api\V1\Requests;

class UpdateMediaRequest extends BaseFormRequest
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
            'is_comments_enabled' => ['sometimes', 'boolean'],
        ];
    }
}
