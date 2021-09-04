<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Requests\MediaRequest;
use Aparlay\Core\Api\V1\Resources\MediaCollection;
use Aparlay\Core\Api\V1\Resources\MediaResource;
use Aparlay\Core\Services\MediaService;
use Aparlay\Core\Services\UploadService;
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
    public function index(): Response
    {
        $type = request()?->input('type') ?? '';

        return $this->response(new MediaCollection(MediaService::getByType($type)), '', Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource.
     */
    public function listByUser(User $user): Response
    {
        return $this->response(new MediaCollection(MediaService::getByUser($user)), '', Response::HTTP_OK);
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

    public function upload(): Response
    {
        $result = UploadService::chunkUpload();

        return $this->response($result['data'], '', $result['code']);
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
    public function update(Request $request, Media $media): Response
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Media $media): Response
    {
        MediaService::delete($media);

        return $this->response([], '', Response::HTTP_NO_CONTENT);
    }
}
