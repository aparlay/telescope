<?php
namespace Aparlay\Core\Admin\Dto;


use Spatie\DataTransferObject\DataTransferObject;

class AdminUserDocumentDTO extends DataTransferObject
{
    public $status;
    public $reason;

    /**
     * @param $request
     * @return AdminUserDocumentDTO
     * @throws \Spatie\DataTransferObject\Exceptions\UnknownProperties
     */
    public static function fromRequest($request)
    {
        return new self([
            'status' => $request->input('status'),
            'reason' => $request->input('reason'),
        ]);

    }

}
