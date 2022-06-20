<?php

namespace Aparlay\Core\Api\V1\Dto;

use Aparlay\Core\Api\V1\Requests\UpdateMediaRequest;
use Spatie\DataTransferObject\DataTransferObject;

class MediaDTO extends DataTransferObject
{
    public bool $is_comments_enabled;

    public static function fromRequest(UpdateMediaRequest $request): self
    {
        return new self(
            [
                'is_comments_enabled' => (bool) $request->input('is_comments_enabled'),
            ]
        );
    }
}
