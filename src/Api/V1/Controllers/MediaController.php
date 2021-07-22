<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->response(Media::all());
    }

    /**
     * Display a listing of the resource.
     *
     * @param  User  $user
     * @return JsonResponse
     */
    public function listByUser(User $user): JsonResponse
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  Media  $media
     * @return JsonResponse
     */
    public function show(Media $media): JsonResponse
    {
        return $this->response($media);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  Media  $media
     * @return JsonResponse
     */
    public function update(Request $request, Media $media): JsonResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function upload(Request $request): JsonResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Media  $media
     * @return JsonResponse
     */
    public function destroy(Media $media): JsonResponse
    {
        //
    }
}
