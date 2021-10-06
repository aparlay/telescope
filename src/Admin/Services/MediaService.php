<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Core\Admin\Models\Media;
use Aparlay\Core\Admin\Repositories\MediaRepository;
use Illuminate\Support\Collection;

class MediaService
{
    protected MediaRepository $mediaRepository;

    public function __construct()
    {
        $this->mediaRepository = new MediaRepository(new Media());
    }

    public function getList(array $options = [])
    {
        return $this->mediaRepository->getList($options);
    }
}
