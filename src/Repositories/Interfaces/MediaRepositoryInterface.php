<?php

namespace Aparlay\Core\Repositories\Interfaces;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Requests\MediaRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;

interface MediaRepositoryInterface
{
    /**
     * Display a listing of the resource.
     */
    public function getMedias(): array|Collection;

    /**
     * Store a newly created resource in storage.
     *
     * @param MediaRequest $request
     * @return Response
     */
    public function create(MediaRequest $request): Media;
}
