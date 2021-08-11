<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Requests\MediaRequest;
use Aparlay\Core\Api\V1\Resources\MediaResource;
use Aparlay\Core\Jobs\UploadMedia;
use Aparlay\Core\Repositories\MediaRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use MongoDB\BSON\ObjectId;

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
        $user = auth()->user();

        $media = new Media([
            'visibility'  => $request->input('visibility', 0),
            'creator'     => [
                '_id'      => new ObjectId($user->_id),
                'username' => $user->username,
                'avatar'   => $user->avatar,
            ],
            'user_id' => $user->_id,
            'created_by' => $user->_id,
            'description' => $request->input('description'),
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file;

            $media->file = uniqid('tmp_', true).'.'.$file->extension();
            $path = Storage::path('upload').'/'.$media->file;

            if (! $file->storeAs('upload', $path)) {
                $this->error(__('Cannot upload the file.'));
            }
        } elseif (! empty($media->file)
            && ! file_exists(Storage::path('upload').'/'.$media->file)) {
            $this->error(__('Uploaded file does not exists.'));
        }

        $media->save();
        $media->refresh();

        dispatch((new UploadMedia((string) $media->userObj->_id, (string) $media->_id, $media->file))
                     ->onQueue('high'));

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
