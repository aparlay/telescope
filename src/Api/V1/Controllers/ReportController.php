<?php

namespace Aparlay\Core\Api\V1\Controllers;

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
     *
     * @OA\POST(
     *     path="/v1/user/{id}/report",
     *     tags={"user"},
     *     summary="report a user",
     *     description="To report user you need to call this endpoint.",
     *     operationId="reportUser",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="user id to report.",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="reason",
     *         in="query",
     *         description="reason of the user that is going to be reported.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="X-DEVICE-ID",
     *         in="header",
     *         description="unique id of the device user is going to send this request it can be segment.com anonymousId.",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="successful operation",
     *         @OA\Header(
     *             header="X-Rate-Limit-Limit",
     *             description="the maximum number of allowed requests during a period",
     *             @OA\Schema(
     *                 type="integer",
     *                 format="int32"
     *             )
     *         ),
     *         @OA\Header(
     *             header="X-Rate-Limit-Remaining",
     *             description="the remaining number of allowed requests within the current period",
     *             @OA\Schema(
     *                 type="integer",
     *                 format="int32"
     *             )
     *         ),
     *         @OA\Header(
     *             header="X-Rate-Limit-Reset",
     *             description="the number of seconds to wait before having maximum number of allowed requests again",
     *             @OA\Schema(
     *                 type="integer",
     *                 format="int32"
     *             )
     *         ),
     *         @OA\JsonContent(ref="#/components/schemas/Report"),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/401"),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="DATA VALIDATION FAILED",
     *         @OA\JsonContent(ref="#/components/schemas/422"),
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="TOO MANY REQUESTS",
     *         @OA\JsonContent(ref="#/components/schemas/429"),
     *     ),
     * )
     */
    public function user(User $user, ReportRequest $request): Response
    {
        if (($loggedInUser = Auth::user()) && Gate::forUser($loggedInUser)->denies('interact', $user->_id)) {
            return $this->error('You cannot report this user at the moment.', [], Response::HTTP_FORBIDDEN);
        }

        $report = $this->reportService->createUserReport($user, $request);

        return $this->response(new ReportResource($report), '', Response::HTTP_CREATED);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\POST(
     *     path="/v1/media/{id}/report",
     *     tags={"media"},
     *     summary="report a media",
     *     description="To report media you need to call this endpoint.",
     *     operationId="reportMedia",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="user id to report.",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="reason",
     *         in="query",
     *         description="reason of the media that is going to be reported.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="X-DEVICE-ID",
     *         in="header",
     *         description="unique id of the device user is going to send this request it can be segment.com anonymousId.",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="successful operation",
     *         @OA\Header(
     *             header="X-Rate-Limit-Limit",
     *             description="the maximum number of allowed requests during a period",
     *             @OA\Schema(
     *                 type="integer",
     *                 format="int32"
     *             )
     *         ),
     *         @OA\Header(
     *             header="X-Rate-Limit-Remaining",
     *             description="the remaining number of allowed requests within the current period",
     *             @OA\Schema(
     *                 type="integer",
     *                 format="int32"
     *             )
     *         ),
     *         @OA\Header(
     *             header="X-Rate-Limit-Reset",
     *             description="the number of seconds to wait before having maximum number of allowed requests again",
     *             @OA\Schema(
     *                 type="integer",
     *                 format="int32"
     *             )
     *         ),
     *         @OA\JsonContent(ref="#/components/schemas/Report"),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/401"),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="DATA VALIDATION FAILED",
     *         @OA\JsonContent(ref="#/components/schemas/422"),
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="TOO MANY REQUESTS",
     *         @OA\JsonContent(ref="#/components/schemas/429"),
     *     ),
     * )
     */
    public function media(Media $media, ReportRequest $request): Response
    {
        if (($loggedInUser = Auth::user()) && Gate::forUser($loggedInUser)->denies('interact', $media->created_by)) {
            return $this->error('You cannot report this video at the moment.', [], Response::HTTP_FORBIDDEN);
        }

        $report = $this->reportService->createMediaReport($media, $request);

        return $this->response(new ReportResource($report), '', Response::HTTP_CREATED);
    }
}
