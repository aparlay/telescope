<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Core\Admin\Dto\AdminUserDocumentDTO;
use Aparlay\Core\Api\V1\Dto\UserDocumentDto;
use Aparlay\Core\Models\UserDocument;

class UserDocumentService extends AdminBaseService
{


    public function update(UserDocument $userDocument, AdminUserDocumentDTO $dto)
    {
        $userDocument->fill($dto->all())->save();
        return $userDocument;
    }
}
