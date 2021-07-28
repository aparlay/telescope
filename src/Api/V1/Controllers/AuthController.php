<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Requests\UserRequest;
use Aparlay\Core\Api\V1\Requests\LoginRequest;
use Aparlay\Core\Repositories\UserRepository;
use Aparlay\Core\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected $userService;

    protected $userRepository;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(UserService $userService, UserRepository $userRepository)
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
        $this->userService = $userService;
        $this->userRepository = $userRepository;
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
        $loginEntity = $this->userService->findIdentity($request->username);

        $credentials = [$loginEntity => $request->username, 'password'=>$request->password];
        
        if ($token = auth()->attempt($credentials)) {

            $user = auth()->user();
            if($error = $this->userService->isUserEligible($user)) {
                return $this->error($error, [], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            //return $this->response(['success' => true, 'data' => $this->respondWithToken($token), 'message'=> 'Entity has been created successfully!'], Response::HTTP_OK);

            /** COMMENTED BECAUSE IN PROGRESS */
            /* */
            $deviceId = $request->headers->get('X-DEVICE-ID');
            $otpSetting = json_decode($user['setting'], true);
            if(empty($request->otp) || $user->status == User::STATUS_PENDING || $otpSetting['otp'] == true) {
                return $this->userService->requireOtp($user, $loginEntity, $deviceId);
            } else if(!empty($request->otp) && $user->status == User::STATUS_PENDING) {
                $user = $this->userService->validateOtp($user);
            }
            if($user->status == User::STATUS_VERIFIED) {
                return $this->response(['success' => true, 'data' => $this->respondWithToken($token), 'message'=> 'Entity has been created successfully!'], Response::HTTP_OK);
            } 
            
            
        } else {
            return $this->error('Data Validation Failed', [], Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Get the token array structure.
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
    public function register(UserRequest $request)
    {
        $user = User::create(array_merge(
            $request->all(),
            ['password_hash' => Hash::make($request->password)],
            ['status' => User::STATUS_PENDING],
            ['visibility' => User::VISIBILITY_PUBLIC]
        ));

        return $this->response(['success' => true, 'data' => $user], '', Response::HTTP_OK);
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
