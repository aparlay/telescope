<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Requests\LoginRequest;
use Aparlay\Core\Api\V1\Requests\RegisterRequest;
use Aparlay\Core\Api\V1\Resources\RegisterResource;
use Aparlay\Core\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return JsonResponse
     */
    public function token()
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return JsonResponse
     */
    public function changePassword()
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return JsonResponse
     */
    public function validateOtp()
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return JsonResponse
     */
    public function requestOtp()
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response|void
     *
     * @throws ValidationException
     */
    public function login(LoginRequest $request)
    {
        /** Find the identityField (Email/PhoneNumber/Username) based on username */
        $identityField = UserService::getIdentityType($request->username);

        /** Prepare Credentials and attempt the login */
        $credentials = [$identityField => $request->username, 'password' => $request->password];
        if ($token = auth()->attempt($credentials)) {
            /* Check the account status and through exception for suspended/banned/NotFound account */
            if (UserService::isUserEligible(auth()->user())) {
                $result = $this->respondWithToken($token);
                $cookie1 = Cookie::make('__Secure_token', $result['access_token'], $result['token_expired_at'] / 60);
                $cookie2 = Cookie::make(
                    '__Secure_refresh_token',
                    $result['refresh_token'],
                    $result['refresh_token_expired_at'] / 60
                );
                $cookie3 = Cookie::make(
                    '__Secure_username',
                    auth()->user()->username,
                    $result['refresh_token_expired_at'] / 60
                );

                return $this->response($result)->cookie($cookie1)->cookie($cookie2)->cookie($cookie3);
            }
        } else {
            /* Through exception in case of invalid username/password. */
            throw ValidationException::withMessages(['password' => ['Incorrect username or password.']]);
        }
    }

    /**
     * Responsible to prepare the json response containing token and expiry.
     */
    protected function respondWithToken(string $token): array
    {
        return [
            'access_token' => $token,
            'token_expired_at' => auth()->factory()->getTTL() * 60,
            'refresh_token' => $token,
            'refresh_token_expired_at' => auth()->factory()->getTTL() * 60,
        ];
    }

    /**
     * Register a User.
     */
    public function register(RegisterRequest $request): Response
    {
        $user = User::create($request->all());

        return $this->response(
            new RegisterResource($user),
            'Entity has been created successfully!',
            Response::HTTP_CREATED
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function logout(): Response
    {
        auth()->logout();

        Cookie::forget('__Secure_token');
        Cookie::forget('__Secure_refresh_token');
        Cookie::forget('__Secure_username');

        return $this->response([], '', Response::HTTP_NO_CONTENT);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function refresh(): Response
    {
        return $this->response($this->respondWithToken(auth()->refresh()));
    }
}
