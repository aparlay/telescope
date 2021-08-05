<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Requests\MediaRequest;
use Aparlay\Core\Api\V1\Resources\MediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
        $media = Media::create($request->all());
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
