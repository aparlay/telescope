<?php

namespace Aparlay\Core\Api\V1\Resources;

use BeyondCode\ServerTiming\Facades\ServerTiming;

class JsonResource extends \Illuminate\Http\Resources\Json\JsonResource
{
    /**
     * Resolve the resource to an array.
     *
     * @param \Illuminate\Http\Request|null $request
     *
     * @return array
     */
    public function resolve($request = null)
    {
        ServerTiming::start('JsonResource::generateItem');
        $result = parent::resolve($request);
        ServerTiming::stop('JsonResource::generateItem');

        return $result;
    }
}
