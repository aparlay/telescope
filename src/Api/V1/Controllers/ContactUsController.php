<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\Alert;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Aparlay\Core\Api\V1\Requests\ContactUsRequest;

class ContactUsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function send( ContactUsRequest $request): Response
    {
        return $this->response([], Response::HTTP_OK);
    }

}
