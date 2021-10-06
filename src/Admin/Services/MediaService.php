<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Core\Admin\Models\Media;
use Aparlay\Core\Admin\Repositories\MediaRepository;
use Aparlay\Core\Helpers\Cdn;

class MediaService
{
    protected MediaRepository $mediaRepository;

    public function __construct()
    {
        $this->mediaRepository = new MediaRepository(new Media());
    }

    public function getList()
    {
        $mediaCollection = $this->mediaRepository->getList();

        foreach ($mediaCollection as $collect) {
            $collect->status_text = $collect->status_color;
            $collect->cover = Cdn::cover(! empty($value['file']) ? $value['file'].'.jpg' : 'default.jpg');
        }

        return $mediaCollection;
    }
}
