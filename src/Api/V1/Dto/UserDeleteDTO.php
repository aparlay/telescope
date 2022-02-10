<?php

namespace Aparlay\Core\Api\V1\Dto;

use Illuminate\Http\Request;
use Spatie\DataTransferObject\DataTransferObject;

class UserDeleteDTO extends DataTransferObject
{
    public string $reason;

    public static function fromRequest(Request $request): self
    {
        return new self(
            [
                'reason' => (string) $request->input('reason', ''),
            ]
        );
    }
}
