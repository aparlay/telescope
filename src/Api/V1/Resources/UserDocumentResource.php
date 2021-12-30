<?php

namespace Aparlay\Core\Api\V1\Resources;

use Aparlay\Core\Api\V1\Traits\FilterableResourceTrait;
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
    use FilterableResourceTrait;

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
        $data = [
            '_id' => (string) $this->_id,
            'type' => $this->type,
            'status' => $this->status,
            'url' => $this->temporaryUrl(),
            'status_label' => $this->statusLabel,
            'type_label' => $this->typeLabel,
        ];

        return $this->filtrateFields($this->filter($data));
    }
}
