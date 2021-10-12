<?php

namespace Aparlay\Core\Api\V1\Models;

use Aparlay\Core\Models\MediaVisit as MediaVisitBase;

/**
 * @OA\Schema()
 */
class MediaVisit extends MediaVisitBase
{
    use CreatorFieldTrait;
}
