<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Requests\MediaRequest;
use Aparlay\Core\Api\V1\Resources\MediaResource;
use Aparlay\Core\Repositories\MediaRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MediaController extends Controller
{
    public $repository;

    public function __construct(MediaRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return $this->response($this->repository->getMedias());
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
        $media = $this->repository->create($request);

        if ($request->hasFile('file')) {
            $file = $request->file;

            $media->file = uniqid('tmp_', true).'.'.$file->extension();
            $fileName = time().'.'.$file->extension();

            if (! $file->storeAs('uploads', $fileName)) {
                return $this->error(__('Cannot upload the file.'));
            }
        } elseif (! empty($media->file)
            && ! file_exists(public_path('uploads').'/'.$media->file)) {
            return $this->error(__('Uploaded file does not exists.'));
        }

        $media->save();
        $media->refresh();

        return $this->response(new MediaResource($media), '', Response::HTTP_CREATED);
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
    public function upload(Request $request): Response
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Media $media): Response
    {
    }
}
