<?php

namespace Aparlay\Core\Api\V1\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * @param  Request  $request
     * @param  Exception|Throwable  $exception
     * @return JsonResponse|Response|\Symfony\Component\HttpFoundation\Response
     * @throws Throwable
     */
    public function render($request, Exception|Throwable $exception)
    {
        // detect instance
        if ($exception instanceof UnauthorizedHttpException) {
            $response = [
                'code' => 401,
                'status' => 'ERROR',
                'message' => __('UNAUTHORIZED_REQUEST'),
            ];
            if ($exception->getPrevious() instanceof TokenExpiredException) {
                $response['code'] = $exception->getStatusCode();
                $response['message'] = __('TOKEN_EXPIRED');
            }

            if ($exception->getPrevious() instanceof TokenInvalidException) {
                $response['code'] = $exception->getStatusCode();
                $response['message'] = __('TOKEN_INVALID');
            }

            if ($exception->getPrevious() instanceof TokenBlacklistedException) {
                $response['code'] = $exception->getStatusCode();
                $response['message'] = __('TOKEN_BLACKLISTED');
            }

            return response()->json($response, $response['code']);
        }

        return parent::render($request, $exception);
    }
}
