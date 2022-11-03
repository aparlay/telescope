<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Dto\MediaDTO;
use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Requests\MediaFeedRequest;
use Aparlay\Core\Api\V1\Requests\MediaRequest;
use Aparlay\Core\Api\V1\Requests\UpdateMediaRequest;
use Aparlay\Core\Api\V1\Resources\MediaCollection;
use Aparlay\Core\Api\V1\Resources\MediaFeedsCollection;
use Aparlay\Core\Api\V1\Resources\MediaResource;
use Aparlay\Core\Api\V1\Services\MediaService;
use Aparlay\Core\Api\V1\Services\UploadService;
use Aparlay\Core\Models\Enums\UserInterestedIn;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MediaController extends Controller
{
    protected $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
        $this->authorizeResource(Media::class, 'media');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(MediaFeedRequest $request): Response
    {
        if ($request->type !== null) {
            $collection = new MediaCollection($this->mediaService->getFeedByType($request->type));
        } else {
            $collection = new MediaFeedsCollection($this->mediaService->getPublicFeeds($request->gender_preferences));
        }

        return $this->response($collection, '', Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource.
     */
    public function listByUser(User $user): Response
    {
        return $this->response(new MediaCollection($this->mediaService->getByUser($user)), '', Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param MediaRequest $request
     * @return Response
     */
    public function store(MediaRequest $request): Response
    {
        return $this->response(
            new MediaResource($this->mediaService->create($request)),
            '',
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Media $media): Response
    {
        return $this->response(new MediaResource($media));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMediaRequest $request, Media $media): Response
    {
        return $this->response(
            new MediaResource($this->mediaService->update($media, MediaDTO::fromRequest($request))),
            '',
            Response::HTTP_CREATED
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Media $media): Response
    {
        $this->mediaService->delete($media);

        return $this->response([], '', Response::HTTP_NO_CONTENT);
    }

    /**
     * Upload media file.
     *
     * @param  Request  $request
     * @return Response
     */
    public function streamUpload(Request $request)
    {
        $result = UploadService::stream($request);

        return response($result['data'], $result['code'], []);
    }

    /**
     * Upload media file.
     *
     * @param  Request  $request
     * @return Response
     * @throws \Flow\FileLockException
     * @throws \Flow\FileOpenException
     */
    public function splitUpload(Request $request): Response
    {
        $result = UploadService::split($request);

        return response($result['data'], $result['code'], []);
    }
}
