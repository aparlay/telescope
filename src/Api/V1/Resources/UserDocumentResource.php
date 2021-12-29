<?php

namespace Aparlay\Core\Api\V1\Resources;

use Aparlay\Core\Models\Enums\UserDocumentStatus;
use Aparlay\Core\Models\Enums\UserDocumentType;
use Aparlay\Core\Models\UserDocument;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MongoDB\BSON\ObjectId;

/**
 * @mixin UserDocument
 */
class UserDocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     *
     * @return array
     * @throws \Exception
     */
    public function toArray($request)
    {
        return [
            '_id' => (string) $this->_id,
            'type' => $this->type,
            'status' => $this->status,
            'url' => $this->temporaryUrl(),
            'status_label' => UserDocumentStatus::from($this->status)->name,
            'type_label' => UserDocumentType::from($this->type)->name,
        ];
    }
}
