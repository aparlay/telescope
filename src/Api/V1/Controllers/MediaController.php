<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Requests\MediaRequest;
use Aparlay\Core\Api\V1\Resources\MediaResource;
use Aparlay\Core\Repositories\MediaRepository;
use Aparlay\Core\Services\BackBlaze;
use Aparlay\Core\Services\UploadService;
use Flow\Config;
use Flow\File;
use Flow\Request as FlowRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public $repository;

    public function __construct(MediaRepository $repository)
    {
        $this->repository = $repository;
        $this->authorizeResource(Media::class, 'media');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return $this->response([], Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource.
     */
    public function listByUser(User $user): Response
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param MediaRequest $request
     * @return Response
     */
    public function store(MediaRequest $request): Response
    {
        return $this->repository->store($request);
    }

    public function upload(): Response
    {
        $result = UploadService::chunkUpload();

        return $this->response($result['data'], '', $result['code']);
    }

    public function uploadToken()
    {
        $b2 = new BackBlaze();
        $result = $b2->generateToken();
        $result['filename'] = uniqid(auth()->user()->_id.'_', false);

        return $result;
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
    }
}
