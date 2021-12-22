<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\MediaLike;
use Aparlay\Core\Api\V1\Resources\MediaLikeResource;
use Aparlay\Core\Api\V1\Services\MediaLikeService;
use Illuminate\Http\Response;

class MediaLikeController extends Controller
{
    protected $mediaLikeService;

    public function __construct(MediaLikeService $mediaLikeService)
    {
        $this->mediaLikeService = $mediaLikeService;

        if (auth()->check()) {
            $this->mediaLikeService->setUser(auth()->user());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Media $media): Response
    {
        $this->authorize('create', [MediaLike::class, $media]);

        $response = $this->mediaLikeService->create($media);

        return $this->response(new MediaLikeResource($response['data']), '', $response['statusCode']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Media $media): Response
    {
        $this->authorize('delete', [MediaLike::class, $media]);

        // Unlike the media or throw exception if not liked
        $response = $this->mediaLikeService->unLike($media);

        return $this->response($response, '', Response::HTTP_NO_CONTENT);
    }
}
