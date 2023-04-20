<?php

namespace Aparlay\Core\Api\V1\Swagger\Definitions;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(type="object", title="UploadToken", description="upload token response")
 */
class UploadToken
{
    /**
     * @OA\Property(type="object",
     *      @OA\Property(property="account_id", type="string", example="0a6daeaf64b4"),
     *      @OA\Property(property="api_url", type="string", example="https://api000.backblazeb2.com"),
     *      @OA\Property(property="bucket_name", type="string", example="waptap-videos"),
     *      @OA\Property(property="bucket_id", type="string", example="e0fa06dd2a4eba4f76740b14"),
     *      @OA\Property(property="authorization_token", type="string", example="4_0000a6daeaf64b40000000002_019a68b0_803eb3_acct_m0G7t4RJShhBGxL4YfWNZaaCRO8="),
     *      @OA\Property(property="absolute_minimum_part_size", type="integer", example=5000000),
     *      @OA\Property(property="recommended_part_size", type="integer", example=100000000),
     *      @OA\Property(property="filename", type="string", example="602e125eb2a01c3838414439_602f5a96b2276"),
     *      @OA\Property(property="upload_url", type="string", example="https://pod-000-1077-12.backblaze.com/b2api/v2/b2_upload_file/e0fa06dd2a4eba4f76740b14/c000_v0001077_t0017"),
     * )
     */
    public $data;

    /**
     * @OA\Property(format="string", example="Entity has been created successfully!")
     */
    public string $message;

    /**
     * @OA\Property(format="integer", example=201)
     */
    public int $code;

    /**
     * @OA\Property(format="string", example="OK")
     */
    public string $status;
}
