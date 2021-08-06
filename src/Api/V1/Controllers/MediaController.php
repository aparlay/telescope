<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Requests\MediaRequest;
use Aparlay\Core\Api\V1\Resources\MediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use MongoDB\BSON\ObjectId;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->response(Media::all());
    }

    /**
     * Display a listing of the resource.
     *
     * @param  User  $user
     * @return Response
     */
    public function listByUser(User $user): Response
    {
        //
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
            'visibility' => $user->visibility,
            'creator' => [
                '_id' => new ObjectId($user->_id),
                'username' => $user->username,
                'avatar' => $user->avatar
            ],
            'description' => $request->input('description')
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file;

            $media->file = uniqid('tmp_', true) . '.' . $file->extension();
            $fileName = time() . '.' . $file->extension();

            if (!$file->storeAs('uploads', $fileName)) {
                return $this->error(__('Cannot upload the file.'));
            }

        } else if (!empty($media->file)
            && !file_exists(public_path('uploads') . '/' . $media->file)) {
            return $this->error(__('Uploaded file does not exists.'));
        }

        $media->save();
        $media->refresh();

        return $this->response(new MediaResource($media), '', Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  Media  $media
     * @return Response
     */
    public function show(Media $media): Response
    {
        return $this->response(new MediaResource($media));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  Media  $media
     * @return Response
     */
    public function update(Request $request, Media $media): Response
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function upload(Request $request): Response
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Media  $media
     * @return Response
     */
    public function destroy(Media $media): Response
    {
        //
    }
}
