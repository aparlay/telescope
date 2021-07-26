<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Requests\UserRequest;
use Aparlay\Core\Api\V1\Rules\IsValidGender;
use Aparlay\Core\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Validator;

class AuthController extends Controller
{
    protected $userService;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(UserService $userService)
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
        $this->userService = $userService;
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
    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'username'  => 'required',
                'password'  => 'required',
                'otp'       => 'nullable'
            ]
        );

        if ($validator->fails()) {
            return $this->error(
                __('The given data was invalid.'),
                $validator->errors()->toArray(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $loginEntity = $this->getLoginEntity($request->username);

        $credentials = [$loginEntity => $request->username, 'password'=>$request->password];

        if ($token = auth()->attempt($credentials)) {

            $user = auth()->user();
            if($error = $this->userService->isUserEligible($user)) {
                return $this->error(
                    __($error),
                    $validator->errors()->toArray(),
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            if(empty($request->otp) && $user->status == User::STATUS_PENDING) {
                $this->userService->sendOtp($user, $loginEntity);
            } else if(!empty($request->otp) && $user->status == User::STATUS_PENDING) {
                $user = $this->userService->validateOtp($user);
            }

            if($user->status == User::STatusApproved) {
                return $this->response(['success' => true, 'data' => $this->respondWithToken($token), 'message'=> 'Entity has been created successfully!'], Response::HTTP_OK);
            }
        } else {
            return $this->error(
                __('Data Validation Failed'),
                $validator->errors()->toArray(),
                Response::HTTP_UNAUTHORIZED
            );
        }
    }

    private function getLoginEntity($username) {
        switch($username) {
            case filter_var( $username, FILTER_VALIDATE_EMAIL ):
                return "email";
            case is_numeric($username):
                return "phone_number";
            default:
                return "username";
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
        $validator = Validator::make(
            $request->all(),
            [
                'email' => ['nullable','email','unique:users','max:100', 'required_without:phone_number'],
                'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
                'password_confirmation' => ['required'],
                'gender' => ['required','numeric', new IsValidGender()],
                'username' => ['nullable','unique:users','min:6','max:20'],
                'phone_number' => ['nullable','numeric','required_without:email'],
            ]
        );

        if ($validator->fails()) {
            return $this->error(
                __('Data Validation Failed'),
                $validator->errors()->toArray(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

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
