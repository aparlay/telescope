<?php

namespace Aparlay\Core\Admin\Services;

use Aparlay\Core\Admin\Models\Media;
use Aparlay\Core\Admin\Repositories\MediaRepository;
use Aparlay\Core\Helpers\Cdn;
use Illuminate\Http\Request;

class MediaService
{
    protected MediaRepository $mediaRepository;

    public function __construct()
    {
        $this->mediaRepository = new MediaRepository(new Media());
    }

    public function getList()
    {
        $mediaCollection = $this->mediaRepository->all();

        foreach ($mediaCollection as $collect) {
            $collect->status_text = $collect->status_color;
            $collect->cover = Cdn::cover(! empty($value['file']) ? $value['file'].'.jpg' : 'default.jpg');
        }

        return $mediaCollection;
    }

    /**
     * @param $id
     */
    public function find($id)
    {
        $media = $this->mediaRepository->find($id);

        $statusBadge = [
            'status' => $media->status_color['text'],
            'color' => $media->status_color['color'],
        ];

        $media->status_badge = $statusBadge;

        return $media;
    }

    /**
     * @param $id
     */
    public function updateMedia(Request $request, $id)
    {
        $data = [
            'description' => $request->description,
            'status' => $request->status,
            // 'skin_score' => $request->Media->skin_score,
        ];

        return $this->mediaRepository->update($data, $id);
    }
}
