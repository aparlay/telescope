<?php

namespace Aparlay\Core\Api\V1\Dto;

use Aparlay\Core\Api\V1\Requests\ReportRequest;
use Spatie\DataTransferObject\DataTransferObject;

class ReportDTO extends DataTransferObject
{
    public string $reason;

    public $created_by;
    public $updated_by;

    public static function fromRequest(ReportRequest $reportRequest): self
    {
        return new self(
            [
                'reason' => $reportRequest->input('reason'),
            ]
        );
    }
}
