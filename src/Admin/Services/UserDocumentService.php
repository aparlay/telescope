<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Core\Admin\Dto\AdminUserDocumentDTO;
use Aparlay\Core\Api\V1\Dto\UserDocumentDto;
use Aparlay\Core\Models\Enums\UserDocumentStatus;
use Aparlay\Core\Models\UserDocument;

class UserDocumentService extends AdminBaseService
{
    public function update(UserDocument $userDocument, AdminUserDocumentDTO $dto)
    {
        $userDocument->status = (int) $dto->status;

        if ((int) $dto->status === UserDocumentStatus::REJECTED->value) {
            $userDocument->reject_reason = $dto->reject_reason;
        }
        $userDocument->save();

        return $userDocument;
    }
}
