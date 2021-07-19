<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function listByUser(User $user)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  Media  $media
     * @return Response
     */
    public function show(Media $media)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  Media  $media
     * @return Response
     */
    public function update(Request $request, Media $media)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function upload(Request $request)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Media  $media
     * @return Response
     */
    public function destroy(Media $media)
    {
        //
    }
}
