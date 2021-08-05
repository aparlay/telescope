<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/v1/cache",
     *     tags={"site"},
     *     summary="Get current user data",
     *     description="To Login an alreadyregistered user you need to call this endpoint.",
     *     operationId="cache",
     *     security={{"bearerAuth": {}}},
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
     *         @OA\JsonContent(ref="#/components/schemas/User"),
     *     ),
     * )
     *
     * @return JsonResponse
     */
    public function cache()
    {
        include_once app_path('preload.php');
        $current = realpath_cache_size();
        $value = ini_get('realpath_cache_size');
        $value = trim($value);
        $last = strtolower(substr($value, -1));
        if (in_array($last, ['g', 'm', 'k'], true)) {
            $value = (int) substr($value, 0, -1);

            $value *= match ($last) {
                'g' => 1024 * 1024 * 1024,
                'm' => 1024 * 1024,
                'k' => 1024,
            };
        }
        $ttl = ini_get('realpath_cache_ttl');
        $percentUsed = $current * 100 / $value;

        return response()->json([
                                    'current' => $current,
                                    'max' => $value,
                                    'percent' => $percentUsed,
                                    'ttl' => $ttl,
                                ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Get(
     *     path="/v1/health",
     *     tags={"site"},
     *     summary="Get current user data",
     *     description="To Login an alreadyregistered user you need to call this endpoint.",
     *     operationId="health",
     *     security={{"bearerAuth": {}}},
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
     *         @OA\JsonContent(ref="#/components/schemas/User"),
     *     ),
     * )
     *
     * @return JsonResponse
     */
    public function health(Request $request)
    {
        return response()->json([
                                ]);
    }
}
