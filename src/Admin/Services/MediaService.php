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
        $data = [];
        if ($request->has('visibility')) {
            $data['visibility'] = ($request->visibility == 'on') ? 1 : 0;
        }
        if ($request->has('is_music_licensed')) {
            $data['is_music_licensed'] = ($request->is_music_licensed == 'on') ? true : false;
        }
        if ($request->has('description')) {
            $data['description'] = $request->description;
        }
        if ($request->has('status')) {
            $data['status'] = $request->status;
        }
        if ($request->has('skin_score')) {
            $data['scores'] = [
                [
                    'type' => 'skin',
                    'score' => $request->skin_score,
                ],
                [
                    'type' => 'awesomeness',
                    'score' => $request->awesomeness_score,
                ],
            ];
        }

        return $this->mediaRepository->update($data, $id);
    }

    public function skinScore()
    {
        return $this->mediaRepository->skinScore();
    }

    public function awesomenessScore()
    {
        return $this->mediaRepository->awesomenessScore();
    }
}
