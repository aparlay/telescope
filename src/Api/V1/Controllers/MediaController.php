<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Resources\MediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return $this->response(Media::all());
    }

    /**
     * Display a listing of the resource.
     */
    public function listByUser(User $user): Response
    {
        return $this->response([], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): Response
    {
        return $this->response([], Response::HTTP_OK);
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
        return $this->response([], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function upload(Request $request): Response
    {
        return $this->response([], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Media $media): Response
    {
        return $this->response([], Response::HTTP_OK);
    }
}
