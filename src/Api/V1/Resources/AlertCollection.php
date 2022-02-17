<?php

namespace Aparlay\Core\Api\V1\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AlertCollection extends ResourceCollection
{
    public $collects = AlertResource::class;
}
