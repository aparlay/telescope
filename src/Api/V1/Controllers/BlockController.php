<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\Block;
use Aparlay\Core\Api\V1\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BlockController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  User  $user
     * @param  Request  $request
     * @return Response
     */
    public function store(User $user, Request $request)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Block  $media
     * @return Response
     */
    public function destroy(Block $media)
    {
        //
    }
}
