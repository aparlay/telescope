<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Requests\ChangePasswordRequest;
use Aparlay\Core\Api\V1\Requests\LoginRequest;
use Aparlay\Core\Api\V1\Requests\RegisterRequest;
use Aparlay\Core\Api\V1\Requests\RequestOtpRequest;
use Aparlay\Core\Api\V1\Requests\ValidateOtpRequest;
use Aparlay\Core\Api\V1\Resources\RegisterResource;
use Aparlay\Core\Api\V1\Services\OtpService;
use Aparlay\Core\Api\V1\Services\UserService;
use Aparlay\Core\Models\Login;
use App\Exceptions\BlockedException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected $userService;
    protected $otpService;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(UserService $userService, OtpService $otpService)
    {
        $this->userService = $userService;
        $this->otpService = $otpService;

        $this->middleware('auth:api', [
            'except' => [
                'login',
                'register',
                'requestOtp',
                'validateOtp',
                'changePassword',
            ],
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function token(): Response
    {
        return $this->response([], Response::HTTP_OK);
    }

    /**
     * @OA\Put(
     *     path="/v1/change-password",
     *     tags={"user"},
     *     summary="Change user password",
     *     description="To Change/Reset Password endpoint help users to reset their forgotten password. if scenario is reset password then user have to send a request-otp request first. after that you have an otp and you will send your reset password request with password, otp and either email or phone_number based on the selected approach in request-otp request. this means otp which is send to email is not valid for phone_number and vice versa. to change password user doesn't need to have an otp just send password and old_password. remember that this request will set a cookies for login too!",
     *     operationId="changePassword",
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="The new password",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="X-DEVICE-ID",
     *         in="header",
     *         description="unique id of the device user is going to send this request it can be segment.com anonymousId.",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="old_password",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="phone_number",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="otp",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\Header(
     *             header="X-Rate-Limit-Limit",
     *             description="the maximum number of allowed requests during a period",
     *             @OA\Schema(
     *                 type="integer",
     *                 format="int32"
     *             )
     *         ),
     *         @OA\Header(
     *             header="X-Rate-Limit-Remaining",
     *             description="the remaining number of allowed requests within the current period",
     *             @OA\Schema(
     *                 type="integer",
     *                 format="int32"
     *             )
     *         ),
     *         @OA\Header(
     *             header="X-Rate-Limit-Reset",
     *             description="the number of seconds to wait before having maximum number of allowed requests again",
     *             @OA\Schema(
     *                 type="integer",
     *                 format="int32"
     *             )
     *         ),
     *         @OA\JsonContent(ref="#/components/schemas/Login"),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="DATA VALIDATION FAILED",
     *         @OA\JsonContent(ref="#/components/schemas/422"),
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="TOO MANY REQUESTS",
     *         @OA\JsonContent(ref="#/components/schemas/429"),
     *     ),
     * )
     *
     * Change password.
     *
     * @param  ChangePasswordRequest  $request
     * @return Response
     * @throws BlockedException
     * @throws ValidationException
     */
    public function changePassword(ChangePasswordRequest $request): Response
    {
        /* Change password scenario */
        if ($request->old_password) {
            $user = auth()->user();

            if ($user === null) {
                throw new BlockedException('User not found', null, null, Response::HTTP_NOT_FOUND);
            }

            /* Check user verification */
            $this->userService->isVerified($user);

            /* Check the update permission */
            $this->authorizeResource(User::class, 'user');

            /* Change the password in database table */
            $this->userService->resetPassword($request->password);
        } else {
            /* Forgot password scenario */
            if (! ($user = $this->userService->findByIdentity($request->username))) {
                throw new BlockedException('User not found', null, null, Response::HTTP_NOT_FOUND);
            }

            /* Validate the OTP or Throw exception if OTP is incorrect */
            $this->otpService->validateOtp($request->otp, $request->username, false, true);

            /* verifying user if status is pending */
            if ($this->userService->isUnverified($user)) {
                $this->userService->verify();
            }

            /* Store the new password in database */
            $this->userService->resetPassword($request->password);
        }

        $loginRequest = new LoginRequest(['username' => $user->username, 'password' => $request->password]);
        $loginRequest->headers = $request->headers;

        return $this->login($loginRequest);
    }

    /**
     * @OA\Patch (
     *     path="/v1/validate-otp",
     *     tags={"user"},
     *     summary="validate user otp",
     *     description="on reset password we need to validate otp first you need to send otp with either email or phone number. this endpoint is designed to validate recently sent OTP to your given identity (email/phone_number).",
     *     operationId="validateOtp",
     *     @OA\Parameter(
     *         name="X-DEVICE-ID",
     *         in="header",
     *         description="unique id of the device user is going to send this request it can be segment.com anonymousId.",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="email of user who want to validate otp.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="phone_number",
     *         in="query",
     *         description="phone_number of the user who want to validate the otp.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="otp",
     *         in="query",
     *         description="otp which is sent to user identity eigther email or phone_number.",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\Header(
     *             header="X-Rate-Limit-Limit",
     *             description="the maximum number of allowed requests during a period",
     *             @OA\Schema(
     *                 type="integer",
     *                 format="int32"
     *             )
     *         ),
     *         @OA\Header(
     *             header="X-Rate-Limit-Remaining",
     *             description="the remaining number of allowed requests within the current period",
     *             @OA\Schema(
     *                 type="integer",
     *                 format="int32"
     *             )
     *         ),
     *         @OA\Header(
     *             header="X-Rate-Limit-Reset",
     *             description="the number of seconds to wait before having maximum number of allowed requests again",
     *             @OA\Schema(
     *                 type="integer",
     *                 format="int32"
     *             )
     *         ),
     *         @OA\JsonContent(ref="#/components/schemas/Otp"),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="DATA VALIDATION FAILED",
     *         @OA\JsonContent(ref="#/components/schemas/422"),
     *     ),
     *     @OA\Response(
     *         response=423,
     *         description="LOCKED",
     *         @OA\JsonContent(ref="#/components/schemas/423"),
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="TOO MANY REQUESTS",
     *         @OA\JsonContent(ref="#/components/schemas/429"),
     *     ),
     * )
     *
     * Validate request OTP.
     *
     * @param  ValidateOtpRequest  $request
     * @return Response
     * @throws BlockedException
     * @throws ValidationException
     */
    public function validateOtp(ValidateOtpRequest $request): Response
    {
        /* Find the user based on username */
        if (! ($user = $this->userService->findByIdentity($request->username))) {
            throw new BlockedException(
                'Your user account not found or does not match with password!',
                null,
                'Unprocessable entity',
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        /* Through exception for suspended/banned/NotFound accounts */
        $this->userService->isUserEligible($user);

        /* Validate the OTP or Throw exception if OTP is incorrect */
        $this->otpService->validateOtp($request->otp, $request->username, true);

        /* Find the identityField (Email/Phone Number/Username) based on username and return the response*/
        return $this->response([
            'message' => 'OTP is matched with your '.ucfirst(str_replace('_', ' ', $this->userService->getIdentityType($request->username))),
        ], '', Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/v1/request-otp",
     *     tags={"user"},
     *     summary="Get current user data",
     *     description="sometime (in reset password or resend otp) you need to send otp to either email or phone number. this endpoint is designed to send OTP to your given identity.",
     *     operationId="requestOtp",
     *     @OA\Parameter(
     *         name="X-DEVICE-ID",
     *         in="header",
     *         description="unique id of the device user is going to send this request it can be segment.com anonymousId.",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="email of user who want to validate otp.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="phone_number",
     *         in="query",
     *         description="phone_number of the user who want to validate the otp.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\Header(
     *             header="X-Rate-Limit-Limit",
     *             description="the maximum number of allowed requests during a period",
     *             @OA\Schema(
     *                 type="integer",
     *                 format="int32"
     *             )
     *         ),
     *         @OA\Header(
     *             header="X-Rate-Limit-Remaining",
     *             description="the remaining number of allowed requests within the current period",
     *             @OA\Schema(
     *                 type="integer",
     *                 format="int32"
     *             )
     *         ),
     *         @OA\Header(
     *             header="X-Rate-Limit-Reset",
     *             description="the number of seconds to wait before having maximum number of allowed requests again",
     *             @OA\Schema(
     *                 type="integer",
     *                 format="int32"
     *             )
     *         ),
     *         @OA\JsonContent(ref="#/components/schemas/Otp"),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="DATA VALIDATION FAILED",
     *         @OA\JsonContent(ref="#/components/schemas/422"),
     *     ),
     *     @OA\Response(
     *         response=423,
     *         description="LOCKED",
     *         @OA\JsonContent(ref="#/components/schemas/423"),
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="TOO MANY REQUESTS",
     *         @OA\JsonContent(ref="#/components/schemas/429"),
     *     ),
     * )
     *
     * Request for otp.
     *
     * @param  RequestOtpRequest  $request
     * @return Response
     * @throws BlockedException
     */
    public function requestOtp(RequestOtpRequest $request): Response
    {
        /* Find the user based on username */
        $user = $this->userService->findByIdentity($request->username);

        if (! empty($user)) {
            /* Through exception for suspended/banned/NotFound accounts */
            $this->userService->isUserEligible($user);

            // Send the OTP or Throw exception if send OTP limit is reached
            $this->otpService->sendOtp($user, $request->header('X-DEVICE-ID'));
        }

        /* Find the identityField (Email/PhoneNumber/Username) based on username and return the response*/
        if ($this->userService->getIdentityType($request->username) === Login::IDENTITY_EMAIL) {
            $response = [
                'message' => 'If you enter your email correctly you will receive an OTP email in your inbox soon.',
            ];
        } else {
            $response = [
                'message' => 'If you enter your phone number correctly you will receive an OTP sms soon.',
                'sms_numbers' => $user['phone_number'],
            ];
        }

        return $this->response($response, '', Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/v1/login",
     *     tags={"user"},
     *     summary="Logs user into system",
     *     description="To Login an already registered user you need to call this endpoint. If it's first time login or if user enalbe otp login in their setting you need to send otp together with username/password otherwise a new OTP will send to the user with 418 as response. remember that this request will set a cookies for login too!",
     *     operationId="loginUser",
     *     @OA\Parameter(
     *         name="username",
     *         in="query",
     *         description="The user name for login",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="otp",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="X-DEVICE-ID",
     *         in="header",
     *         description="unique id of the device user is going to send this request it can be segment.com anonymousId.",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\Header(
     *             header="X-Rate-Limit-Limit",
     *             description="the maximum number of allowed requests during a period",
     *             @OA\Schema(
     *                 type="integer",
     *                 format="int32"
     *             )
     *         ),
     *         @OA\Header(
     *             header="X-Rate-Limit-Remaining",
     *             description="the remaining number of allowed requests within the current period",
     *             @OA\Schema(
     *                 type="integer",
     *                 format="int32"
     *             )
     *         ),
     *         @OA\Header(
     *             header="X-Rate-Limit-Reset",
     *             description="the number of seconds to wait before having maximum number of allowed requests again",
     *             @OA\Schema(
     *                 type="integer",
     *                 format="int32"
     *             )
     *         ),
     *         @OA\JsonContent(ref="#/components/schemas/Login"),
     *     ),
     *     @OA\Response(
     *         response=418,
     *         description="I'M A TEAPOT",
     *         @OA\JsonContent(ref="#/components/schemas/418"),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="DATA VALIDATION FAILED",
     *         @OA\JsonContent(ref="#/components/schemas/422"),
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="TOO MANY REQUESTS",
     *         @OA\JsonContent(ref="#/components/schemas/429"),
     *     ),
     * )
     *
     * Login a user.
     *
     * @param  LoginRequest  $request
     * @return Response|void
     *
     * @throws BlockedException
     * @throws ValidationException
     */
    public function login(LoginRequest $request)
    {
        /** Find the identityField (Email/PhoneNumber/Username) based on username */
        $identityField = $this->userService->getIdentityType($request->username);

        /** Prepare Credentials and attempt the login */
        $credentials = [$identityField => $request->username, 'password' => $request->password];
        if (! ($token = auth()->attempt($credentials))) {
            throw ValidationException::withMessages(['password' => ['Incorrect username or password.']]);
        }

        /** Through exception for suspended/banned/NotFound accounts */
        $user = auth()->user();
        $this->userService->isUserEligible($user);
        $deviceId = $request->header('X-DEVICE-ID');

        if ($this->userService->requireOtp()) {
            if ($request->otp) {
                $this->otpService->validateOtp($request->otp, $request->username);
                $this->userService->verify();
            } else {
                $this->otpService->sendOtp($user, $deviceId);
                $response = [];
                if ($identityField === Login::IDENTITY_PHONE_NUMBER) {
                    $response = [
                        'message' => 'If you enter your phone number correctly you will receive an OTP sms soon.',
                        'sms_numbers' => $user['phone_number'],
                    ];
                } elseif ($identityField === Login::IDENTITY_EMAIL) {
                    $response = [
                        'message' => 'If you enter your email correctly you will receive an OTP email in your inbox soon.',
                    ];
                }
                throw new BlockedException('OTP has been sent.', null, null, Response::HTTP_I_AM_A_TEAPOT, $response);
            }
        }

        /** Prepare and return the json response */
        $result = $this->respondWithToken($token);
        $cookie1 = Cookie::make(
            '__Secure_token',
            $result['token'],
            $result['token_expired_at'] / 60
        );
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

    /**
     * Responsible to prepare the json response containing token and expiry.
     */
    protected function respondWithToken(string $token): array
    {
        return [
            'token' => $token,
            'token_expired_at' => auth()->factory()->getTTL() * 60,
            'refresh_token' => $token,
            'refresh_token_expired_at' => auth()->factory()->getTTL() * 60,
        ];
    }

    /**
     * @OA\Post(
     *     path="/v1/register",
     *     tags={"user"},
     *     summary="Register new user",
     *     description="to register a new users you need to call this endpoint. the flow is according to the screens. user will fill the username/password field with email or phone number as username and a password. after sending request an otp will send to eighter email or phone number via sms. next user will login for the first time with same username/password combination together with otp.",
     *     operationId="registerUser",
     *     @OA\Parameter(
     *         name="X-DEVICE-ID",
     *         in="header",
     *         description="unique id of the device user is going to send this request it can be segment.com anonymousId.",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="referral_id",
     *         in="query",
     *         description="The user id of the referral",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     example="john@aparlay.com",
     *                     property="email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     example="password",
     *                     property="password",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     example=1,
     *                     property="gender",
     *                     type="number"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="successful operation",
     *         @OA\Header(
     *             header="X-Rate-Limit-Limit",
     *             description="the maximum number of allowed requests during a period",
     *             @OA\Schema(
     *                 type="integer",
     *                 format="int32"
     *             )
     *         ),
     *         @OA\Header(
     *             header="X-Rate-Limit-Remaining",
     *             description="the remaining number of allowed requests within the current period",
     *             @OA\Schema(
     *                 type="integer",
     *                 format="int32"
     *             )
     *         ),
     *         @OA\Header(
     *             header="X-Rate-Limit-Reset",
     *             description="the number of seconds to wait before having maximum number of allowed requests again",
     *             @OA\Schema(
     *                 type="integer",
     *                 format="int32"
     *             )
     *         ),
     *         @OA\JsonContent(ref="#/components/schemas/Register"),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="DATA VALIDATION FAILED",
     *         @OA\JsonContent(ref="#/components/schemas/422"),
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="TOO MANY REQUESTS",
     *         @OA\JsonContent(ref="#/components/schemas/429"),
     *     ),
     * )
     *
     * Register a User.
     */
    public function register(RegisterRequest $request): Response
    {
        $user = User::create($request->all());
        $deviceId = $request->header('X-DEVICE-ID');

        $this->otpService->sendOtp($user, $deviceId);

        return $this->response(
            new RegisterResource($user),
            'Entity has been created successfully!',
            Response::HTTP_CREATED
        );
    }

    /**
     * @OA\Delete (
     *     path="/v1/logout",
     *     tags={"user"},
     *     summary="remove cookie token",
     *     description="To remove cookied loged in users need to call this endpoint it only remove browser cookies.",
     *     operationId="logout",
     *     @OA\Parameter(
     *         name="username",
     *         in="query",
     *         description="The user name for login",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="X-DEVICE-ID",
     *         in="header",
     *         description="unique id of the device user is going to send this request it can be segment.com anonymousId.",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="refresh_token",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="successful operation",
     *         @OA\Header(
     *             header="X-Rate-Limit-Limit",
     *             description="the maximum number of allowed requests during a period",
     *             @OA\Schema(
     *                 type="integer",
     *                 format="int32"
     *             )
     *         ),
     *         @OA\Header(
     *             header="X-Rate-Limit-Remaining",
     *             description="the remaining number of allowed requests within the current period",
     *             @OA\Schema(
     *                 type="integer",
     *                 format="int32"
     *             )
     *         ),
     *         @OA\Header(
     *             header="X-Rate-Limit-Reset",
     *             description="the number of seconds to wait before having maximum number of allowed requests again",
     *             @OA\Schema(
     *                 type="integer",
     *                 format="int32"
     *             )
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="DATA VALIDATION FAILED",
     *         @OA\JsonContent(ref="#/components/schemas/422"),
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="TOO MANY REQUESTS",
     *         @OA\JsonContent(ref="#/components/schemas/429"),
     *     ),
     * )
     *
     * Remove the specified resource from storage.
     */
    public function logout(): Response
    {
        auth()->logout();

        $cookie1 = Cookie::forget('__Secure_token');
        $cookie2 = Cookie::forget('__Secure_refresh_token');
        $cookie3 = Cookie::forget('__Secure_username');

        return $this->response([], '', Response::HTTP_NO_CONTENT)->cookie($cookie1)->cookie($cookie2)->cookie($cookie3);
    }

    /**
     * @OA\Put(
     *     path="/v1/refresh-token",
     *     tags={"user"},
     *     summary="refresh a token",
     *     description="To refresh a token after expiration time",
     *     operationId="refreshToken",
     *     @OA\Parameter(
     *         name="username",
     *         in="query",
     *         description="The user name for login",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="X-DEVICE-ID",
     *         in="header",
     *         description="unique id of the device user is going to send this request it can be segment.com anonymousId.",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="refresh_token",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\Header(
     *             header="X-Rate-Limit-Limit",
     *             description="the maximum number of allowed requests during a period",
     *             @OA\Schema(
     *                 type="integer",
     *                 format="int32"
     *             )
     *         ),
     *         @OA\Header(
     *             header="X-Rate-Limit-Remaining",
     *             description="the remaining number of allowed requests within the current period",
     *             @OA\Schema(
     *                 type="integer",
     *                 format="int32"
     *             )
     *         ),
     *         @OA\Header(
     *             header="X-Rate-Limit-Reset",
     *             description="the number of seconds to wait before having maximum number of allowed requests again",
     *             @OA\Schema(
     *                 type="integer",
     *                 format="int32"
     *             )
     *         ),
     *         @OA\JsonContent(ref="#/components/schemas/Login"),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="DATA VALIDATION FAILED",
     *         @OA\JsonContent(ref="#/components/schemas/422"),
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="TOO MANY REQUESTS",
     *         @OA\JsonContent(ref="#/components/schemas/429"),
     *     ),
     * )
     *
     * Remove the specified resource from storage.
     */
    public function refresh(): Response
    {
        return $this->response($this->respondWithToken(auth()->refresh()));
    }
}
