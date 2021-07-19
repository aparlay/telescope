<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReportController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  User  $user
     * @param  Request  $request
     * @return Response
     */
    public function user(User $user, Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Media  $user
     * @param  Request  $request
     * @return Response
     */
    public function media(Media $media, Request $request)
    {
        //
    }
}
