<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    /**
     * success response method.
     *
     * @param $result
     * @param  string  $message
     * @param  int  $code
     * @return JsonResponse
     */
    public function response($result, string $message = '', int $code = 200): JsonResponse
    {
        $response = [
            'code' => $code,
            'status' => 'OK',
            'data' => $result,
        ];

        if (! empty($message)) {
            $response['message'] = $message;
        }

        return response()->json($response, 200);
    }

    /**
     * return error response.
     *
     * @param $error
     * @param  array  $errorMessages
     * @param  int  $code
     *
     * @return JsonResponse
     */
    public function error($error, array $errorMessages = [], int $code = 400): JsonResponse
    {
        $response = [
            'code' => $code,
            'status' => 'ERROR',
            'message' => $error,
        ];

        if (! empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}
