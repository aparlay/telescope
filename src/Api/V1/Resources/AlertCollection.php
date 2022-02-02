<?php

namespace Aparlay\Core\Api\V1\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AlertCollection extends ResourceCollection
{
    public $collects = AlertResource::class;
}
