<?php

namespace Aparlay\Core\Api\V1\Controllers;

use App\Models\User;
// use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Requests\LoginRequest;
use Aparlay\Core\Api\V1\Requests\RegisterRequest;
use Aparlay\Core\Api\V1\Resources\RegisterResource;
use Aparlay\Core\Repositories\UserRepository;
use Aparlay\Core\Services\OtpService;
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
        $this->userRepo = new UserRepository();
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
        /** Find the identityField (Email/PhoneNumber/Username) based on username */
        $identityField = UserService::getIdentityType($request->username);

        /** Prepare Credentials and attempt the login */
        $credentials = [$identityField => $request->username, 'password' => $request->password];
        if (!($token = auth()->attempt($credentials))) {
            throw ValidationException::withMessages(['password' => ['Incorrect username or password.']]);
        }

        /** Through exception for suspended/banned/NotFound accounts */
        $user = auth()->user();
        $elligible = UserService::isUserEligible($user);

        $deviceId = $request->headers->get('X-DEVICE-ID');
        
        if (UserService::isUnverified($user)) {
            if ($request->otp) {
                OtpService::validateOtp($request->otp, $request->username);
                $this->userRepo->verify($user);
            } else {
                return OtpService::sendOtp($user, $loginEntity, $deviceId);
            }
        }

        /** Prepare and return the json response */
        return $this->response($this->respondWithToken($token));
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
        if ($user) {
            $deviceId = $request->headers->get('X-DEVICE-ID');
            $identity = $user->phone_number ?? $user->email;
            /** Find the loginEntity (Email/PhoneNumber/Username) based on username */
            $loginEntity = UserService::getIdentityType($identity);
            OtpService::sendOtp($user, $loginEntity, $deviceId);
        }
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
