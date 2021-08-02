<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Requests\LoginRequest;
use Aparlay\Core\Api\V1\Requests\RegisterRequest;
use Aparlay\Core\Api\V1\Resources\RegisterResource;
use Aparlay\Core\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return JsonResponse
     */
    public function changePassword()
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return JsonResponse
     */
    public function validateOtp()
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return JsonResponse
     */
    public function requestOtp()
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request  $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(LoginRequest $request)
    {
        /** Find the loginEntity (Email/PhoneNumber/Username) based on username */
        $loginEntity = UserService::findIdentity($request->username);

        /** Prepare Credentials and attempt the login */
        $credentials = [$loginEntity => $request->username, 'password'=>$request->password];        
        if ($token = auth()->attempt($credentials)) {

            /** Check the account status and through exception for suspended/banned/NotFound account */
            if(UserService::isUserEligible(auth()->user())) {

                /** Prepare and return the json response */
                return $this->response($this->respondWithToken($token), 'Entity has been created successfully!', Response::HTTP_OK);
            }
        } else {
            /** Through exception in case of invalid username/password. */
            throw ValidationException::withMessages(['password' => ['Incorrect username or password.']]);
        }
    }

    /**
     * Responsible to prepare the json response containing token and expiry
     *
     * @param  string  $token
     *
     * @return array
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
     *
     * @return JsonResponse
     */
    public function register(RegisterRequest $request)
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
     *
     * @return JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return $this->response([], '', Response::HTTP_NO_CONTENT);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        return $this->response($this->respondWithToken(auth()->refresh()));
    }
}
