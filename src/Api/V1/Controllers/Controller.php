<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;

/**
 */
class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    public $repository;

    /**
     * success response method.
     *
     * @param $result
     * @param  string  $message
     * @param  int  $code
     * @param  array  $headers
     * @return Response
     */
    public function response($result, string $message = '', int $code = 200, array $headers = []): Response
    {
        $response = [
            'code' => $code,
            'status' => 'OK',
            'data' => $result,
        ];

        if (! empty($message)) {
            $response['message'] = $message;
        }

        return response($response, $code, $headers);
    }

    /**
     * return error response.
     *
     * @param $error
     * @param  array  $errorMessages
     * @param  int  $code
     * @param  array  $headers
     * @return Response
     */
    public function error($error, array $errorMessages = [], int $code = 400, array $headers = []): Response
    {
        $response = [
            'code' => $code,
            'status' => 'ERROR',
            'message' => $error,
        ];

        if (! empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response($response, $code, $headers);
    }
}
