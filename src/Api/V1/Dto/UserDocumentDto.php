<?php

namespace Aparlay\Core\Api\V1\Dto;

use Aparlay\Core\Api\V1\Requests\ReportRequest;
use Aparlay\Core\Api\V1\Requests\UserDocumentRequest;
use Spatie\DataTransferObject\DataTransferObject;

class UserDocumentDto extends DataTransferObject
{
    public $file;
    public $type;

    /**
     * @param UserDocumentRequest $userDocumentRequest
     * @return static
     * @throws \Spatie\DataTransferObject\Exceptions\UnknownProperties
     */
    public static function fromRequest(UserDocumentRequest $userDocumentRequest): self
    {
        return new self(
            [
                'type' => $userDocumentRequest->input('type'),
                'file' => $userDocumentRequest->file('file'),
            ]
        );
    }

}
