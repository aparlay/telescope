<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\Version;
use Illuminate\Http\JsonResponse;

class VersionController extends Controller
{
    /**
     * Display the specified resource.
     * @OA\Get(
     *     path="/v1/version/{os}/{version}",
     *     tags={"site"},
     *     summary="Get latest version of the application for the givven os",
     *     description="To Login an alreadyregistered user you need to call this endpoint.",
     *     operationId="version",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="os",
     *         in="path",
     *         description="Client os android or ios",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="version",
     *         in="path",
     *         description="The current version of client app",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
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
     *         @OA\JsonContent(ref="#/components/schemas/Login"),
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
     *
     * @param string $os
     * @param string $version
     * @return JsonResponse
     */
    public function show(string $os, string $version): JsonResponse
    {
        $models = Version::os($os)
            ->app('waptap')
            ->latestFirst()
            ->get();

        if (empty($models)) {
            return response()->json(['message' => 'Record not found.'], 404);
        }

        $requireForceUpdate = false;
        foreach ($models as $model) {
            if ($model['is_force_update']) {
                $compareResult = version_compare($version, $model['version']);
                if ($compareResult === -1) {
                    $requireForceUpdate = true;
                }
            }
        }

        return response()->json([
            'require_force_update' => $requireForceUpdate,
            'version' => $models[0],
        ]);
    }
}