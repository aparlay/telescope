<?php

namespace Aparlay\Core\Api\V1\Dto;

use Aparlay\Core\Api\V1\Requests\ReportRequest;
use Spatie\DataTransferObject\DataTransferObject;

class ReportDTO extends DataTransferObject
{
    public string $reason;

    public static function fromRequest(ReportRequest $reportRequest): self
    {
        return new self(
            [
                'reason' => $reportRequest->input('post'),
            ]
        );
    }
}
