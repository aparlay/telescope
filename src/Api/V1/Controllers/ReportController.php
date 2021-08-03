<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\Report;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Notifications\ReportSent;
use Aparlay\Core\Api\V1\Resources\ReportResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use MongoDB\BSON\ObjectId;

class ReportController extends Controller
{
    /**
     * Store a newly created resource in storage.
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
     *
     * @param  User  $user
     * @param  Request  $request
     * @return Response
     */
    public function user(User $user, Request $request): Response
    {
        if (($loggedInUser = Auth::user()) && Gate::forUser($loggedInUser)->denies('interact', $user->_id)) {
            return $this->error('You cannot report this user at the moment.', [], Response::HTTP_FORBIDDEN);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'reason' => 'required|max:255',
            ]
        );

        if ($validator->fails()) {
            return $this->error(
                __('The given data was invalid.'),
                $validator->errors()->toArray(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $model = new Report([
                                'reason' => $request->post('reason'),
                                'type' => Report::TYPE_MEDIA,
                                'status' => Report::STATUS_REPORTED,
                                'user_id' => new ObjectId($user->_id),
                            ]);
        $model->save();
        $model->notify(new ReportSent());

        return $this->response(new ReportResource($model), '', Response::HTTP_CREATED);
    }

    /**
     * Store a newly created resource in storage.
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
     *
     * @param  Media  $media
     * @param  Request  $request
     * @return Response
     */
    public function media(Media $media, Request $request): Response
    {
        if (($loggedInUser = Auth::user()) && Gate::forUser($loggedInUser)->denies('interact', $media->created_by)) {
            return $this->error('You cannot report this video at the moment.', [], Response::HTTP_FORBIDDEN);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'reason' => 'required|max:255',
            ]
        );

        if ($validator->fails()) {
            return $this->error(
                __('The given data was invalid.'),
                $validator->errors()->toArray(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $model = new Report([
                                'reason' => $request->post('reason'),
                                'type' => Report::TYPE_MEDIA,
                                'status' => Report::STATUS_REPORTED,
                                'media_id' => new ObjectId($media->_id),
                            ]);
        $model->save();
        $model->notify(new ReportSent());

        return $this->response(new ReportResource($model), '', Response::HTTP_CREATED);
    }
}
