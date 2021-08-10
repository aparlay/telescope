<?php

namespace Aparlay\Core\Repositories;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Requests\MediaRequest;
use Aparlay\Core\Repositories\Interfaces\MediaRepositoryInterface;
use Aparlay\Core\Services\MediaService;
use Illuminate\Database\Eloquent\Collection;

class MediaRepository implements MediaRepositoryInterface
{
    public function getMedias(): array | Collection
    {
        return Media::all();
    }

    public function create(MediaRequest $request): Media
    {
        return MediaService::create($request);
    }
}
