<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserDocumentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        return $this->response([], Response::HTTP_OK);
    }
}
