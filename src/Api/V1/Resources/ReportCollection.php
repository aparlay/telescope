<?php

namespace Aparlay\Core\Api\V1\Resources;

class ReportCollection extends AbstractResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = ReportResource::class;
}
