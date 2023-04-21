<?php

namespace Aparlay\Core\Admin\Dto;

use Spatie\DataTransferObject\DataTransferObject;

class AdminUserDocumentDTO extends DataTransferObject
{
    public $status;
    public $reject_reason;

    /**
     * @param mixed $request
     *
     * @throws \Spatie\DataTransferObject\Exceptions\UnknownProperties
     *
     * @return AdminUserDocumentDTO
     */
    public static function fromRequest($request)
    {
        return new self([
            'status' => $request->input('status'),
            'reject_reason' => $request->input('reject_reason'),
        ]);
    }
}
