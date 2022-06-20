<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Dto\ReportDTO;
use Aparlay\Core\Api\V1\Models\Alert;
use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\MediaComment;
use Aparlay\Core\Api\V1\Models\Report;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Requests\ReportRequest;
use Aparlay\Core\Api\V1\Resources\ReportResource;
use Aparlay\Core\Api\V1\Services\ReportService;
use Illuminate\Auth\Access\AuthorizationException;
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
     * @throws AuthorizationException
     */
    public function user(User $user, ReportRequest $request): Response
    {
        if (auth()->check()) {
            $this->authorize('user', [Report::class, $user]);
        }

        $this->injectAuthUser($this->reportService);

        $report = $this->reportService->createUserReport($user, ReportDTO::fromRequest($request));

        return $this->response(new ReportResource($report), '', Response::HTTP_CREATED);
    }

    /**
     * Store a newly created resource in storage.
     * @throws AuthorizationException
     */
    public function comment(MediaComment $mediaComment, ReportRequest $request): Response
    {
        if (auth()->check()) {
            $this->authorize('comment', [Report::class, $mediaComment->mediaObj->creatorObj]);
        }

        $this->injectAuthUser($this->reportService);
        $report = $this->reportService->createCommentReport($mediaComment, ReportDTO::fromRequest($request));

        return $this->response(new ReportResource($report), '', Response::HTTP_CREATED);
    }

    /**
     * Store a newly created resource in storage.
     * @throws AuthorizationException
     */
    public function media(Media $media, ReportRequest $request): Response
    {
        if (auth()->check()) {
            $this->authorize('media', [Report::class, $media->creatorObj]);
        }

        $this->injectAuthUser($this->reportService);

        $report = $this->reportService->createMediaReport($media, ReportDTO::fromRequest($request));

        return $this->response(new ReportResource($report), '', Response::HTTP_CREATED);
    }
}
