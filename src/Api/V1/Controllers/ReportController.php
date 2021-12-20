<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Dto\ReportDTO;
use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Requests\ReportRequest;
use Aparlay\Core\Api\V1\Resources\ReportResource;
use Aparlay\Core\Api\V1\Services\ReportService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function user(User $user, ReportRequest $request): Response
    {
        if (($loggedInUser = Auth::user()) && Gate::forUser($loggedInUser)->denies('interact', $user->_id)) {
            return $this->error('You cannot report this user at the moment.', [], Response::HTTP_FORBIDDEN);
        }

        $report = $this->reportService->createUserReport($user, ReportDTO::fromRequest($request));

        return $this->response(new ReportResource($report), '', Response::HTTP_CREATED);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function media(Media $media, ReportRequest $request): Response
    {
        if (($loggedInUser = Auth::user()) && Gate::forUser($loggedInUser)->denies('interact', $media->created_by)) {
            return $this->error('You cannot report this video at the moment.', [], Response::HTTP_FORBIDDEN);
        }

        $report = $this->reportService->createMediaReport($media, ReportDTO::fromRequest($request));

        return $this->response(new ReportResource($report), '', Response::HTTP_CREATED);
    }
}
