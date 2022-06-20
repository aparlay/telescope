<?php

namespace Aparlay\Core\Api\V1\Dto;

use Aparlay\Core\Api\V1\Requests\UpdateMediaRequest;
use Illuminate\Http\Request;
use Spatie\DataTransferObject\DataTransferObject;

class MediaDTO extends DataTransferObject
{
    public string $reason;

    public static function fromRequest(UpdateMediaRequest $request): self
    {
        return new self(
            [
                'is_comments_enabled' => (bool) $request->input('is_comments_enabled'),
            ]
        );
    }
}
