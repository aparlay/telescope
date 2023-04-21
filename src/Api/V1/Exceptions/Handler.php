<?php

namespace Aparlay\Core\Api\V1\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenBlacklistedException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        TokenBlacklistedException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash  = [
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
        });
    }

    /**
     * @param Request $request
     *
     * @throws Throwable
     *
     * @return JsonResponse|Response|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Exception|Throwable $e)
    {
        // detect instance
        if ($e instanceof UnauthorizedHttpException) {
            $response = [
                'code' => 401,
                'status' => 'ERROR',
                'message' => __('UNAUTHORIZED_REQUEST'),
            ];
            if ($e->getPrevious() instanceof TokenExpiredException) {
                $response['code']    = $e->getStatusCode();
                $response['message'] = __('TOKEN_EXPIRED');
            }

            if ($e->getPrevious() instanceof TokenInvalidException) {
                $response['code']    = $e->getStatusCode();
                $response['message'] = __('TOKEN_INVALID');
            }

            if ($e->getPrevious() instanceof TokenBlacklistedException) {
                $response['code']    = $e->getStatusCode();
                $response['message'] = __('TOKEN_BLACKLISTED');
            }

            return response()->json($response, $response['code']);
        }

        return parent::render($request, $e);
    }
}
