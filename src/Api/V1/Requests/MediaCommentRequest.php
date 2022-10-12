<?php

namespace Aparlay\Core\Api\V1\Requests;

class MediaCommentRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'text' => ['required', 'string', 'min:1', 'max:500'],
        ];
    }
}
