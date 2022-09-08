<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\Alert;
use Aparlay\Core\Api\V1\Resources\AlertResource;
use Aparlay\Core\Api\V1\Services\AlertService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AlertController extends Controller
{
    protected $alertService;

    public function __construct(AlertService $alertService)
    {
        $this->alertService = $alertService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Alert  $alert
     * @param  Request  $request
     * @return Response
     * @throws AuthorizationException
     */
    public function update(Alert $alert, Request $request): Response
    {
        $this->authorize('update', [Alert::class, $alert]);
        $this->injectAuthUser($this->alertService);

        $response = $this->alertService->visited($alert);

        return $this->response(new AlertResource($response), '', Response::HTTP_OK);
    }
}
