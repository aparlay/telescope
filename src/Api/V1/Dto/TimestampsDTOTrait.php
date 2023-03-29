<?php

namespace Aparlay\Core\Api\V1\Dto;

use Aparlay\Core\Helpers\DT;
use MongoDB\BSON\UTCDateTime;

trait TimestampsDTOTrait
{
    public null|UTCDateTime $created_at;
    public null|UTCDateTime $updated_at;

    public function setTimestamps(?UTCDateTime $createdAt = null, ?UTCDateTime $updatedAt = null): void
    {
        $this->created_at = $createdAt ?: DT::utcNow();
        $this->updated_at = $updatedAt ?: DT::utcNow();
    }
}
