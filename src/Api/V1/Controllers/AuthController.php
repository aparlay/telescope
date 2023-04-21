<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Requests\ChangePasswordRequest;
use Aparlay\Core\Api\V1\Requests\LoginRequest;
use Aparlay\Core\Api\V1\Requests\RegisterRequest;
use Aparlay\Core\Api\V1\Requests\RequestOtpRequest;
use Aparlay\Core\Api\V1\Requests\ValidateOtpRequest;
use Aparlay\Core\Api\V1\Services\OtpService;
use Aparlay\Core\Api\V1\Services\UserService;
use Aparlay\Core\Jobs\KeitaroPostback;
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
        $this->otpService  = $otpService;

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
     */
    public function token(): Response
    {
        return $this->response([], Response::HTTP_OK);
    }

    /**
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
            if (!($user = $this->userService->findByIdentity($request->username))) {
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

        $loginRequest          = new LoginRequest(['username' => $user->username, 'password' => $request->password]);
        $loginRequest->headers = $request->headers;

        return $this->login($loginRequest);
    }

    /**
     * Validate request OTP.
     *
     * @throws BlockedException
     * @throws ValidationException
     */
    public function validateOtp(ValidateOtpRequest $request): Response
    {
        /* Find the user based on username */
        if (!($user = $this->userService->findByIdentity($request->username))) {
            throw new BlockedException(
                'Your user account not found or does not match with password!',
                null,
                'Unprocessable entity',
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        /* Through exception for suspended/banned/NotFound accounts */
        $this->userService->isUserEligibleForLogin($user);

        /* Validate the OTP or Throw exception if OTP is incorrect */
        if ($this->userService->requireOtp()) {
            $this->otpService->validateOtp($request->otp, $request->username);
            $this->userService->verify();
        } else {
            $this->otpService->validateOtp($request->otp, $request->username, true);
        }

        /* Find the identityField (Email/Phone Number/Username) based on username and return the response */
        return $this->response([
            'message' => 'OTP is matched with your ' . ucfirst(str_replace('_', ' ', $this->userService->getIdentityType($request->username))),
        ], '', Response::HTTP_OK);
    }

    /**
     * Request for otp.
     *
     * @throws BlockedException
     */
    public function requestOtp(RequestOtpRequest $request): Response
    {
        /* Find the user based on username */
        $user = $this->userService->findByIdentity($request->username);

        if (!empty($user)) {
            /* Through exception for suspended/banned/NotFound accounts */
            $this->userService->isUserEligibleForLogin($user);

            // Send the OTP or Throw exception if send OTP limit is reached
            $this->otpService->sendOtp($user, $request->header('X-DEVICE-ID'));
        }

        /* Find the identityField (Email/PhoneNumber/Username) based on username and return the response */
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
     * Login a user.
     *
     * @throws BlockedException
     * @throws ValidationException
     *
     * @return Response|void
     */
    public function login(LoginRequest $request)
    {
        /** Find the identityField (Email/PhoneNumber/Username) based on username */
        $identityField = $this->userService->getIdentityType($request->username);

        /** Prepare Credentials and attempt the login */
        $credentials   = [$identityField => $request->username, 'password' => $request->password];

        if (!($token = auth()->attempt($credentials))) {
            throw ValidationException::withMessages(['password' => ['Incorrect username or password.']]);
        }

        /** Through exception for suspended/banned/NotFound accounts */
        $user          = auth()->user();
        $this->userService->isUserEligibleForLogin($user);

        if ($this->userService->requireOtp() && $request->otp) {
            $this->otpService->validateOtp($request->otp, $request->username);
            $this->userService->verify();
        }

        /** Prepare and return the json response */
        $result        = $this->respondWithToken($token);
        $cookie1       = Cookie::make(
            '__Secure_token',
            $result['token'],
            $result['token_expired_at'] / 60
        );
        $cookie2       = Cookie::make(
            '__Secure_refresh_token',
            $result['refresh_token'],
            $result['refresh_token_expired_at'] / 60
        );
        $cookie3       = Cookie::make(
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
            'token_expired_at' => auth()->factory()->getTTL()         * 60,
            'refresh_token' => $token, // auth()->user()->createToken('web-app')->plainTextToken,
            'refresh_token_expired_at' => auth()->factory()->getTTL() * 60,
        ];
    }

    /**
     * Register a User.
     */
    public function register(RegisterRequest $request): Response
    {
        $user                  = User::create($request->all());

        $trackerSubId          = $request->cookie('__Secure_tracker_subid');
        $trackerToken          = $request->cookie('__Secure_tracker_token');
        if ($trackerSubId && $trackerToken) {
            KeitaroPostback::dispatch($trackerSubId, $trackerToken);
            $user->tracking = [
                'keitaro' => [
                    '_subid' => $trackerSubId,
                    '_token' => $trackerToken,
                ],
            ];
        }

        $deviceId              = $request->header('X-DEVICE-ID');

        $this->otpService->sendOtp($user, $deviceId);

        $loginRequest          = new LoginRequest(['username' => $request->email, 'password' => $request->password]);
        $loginRequest->headers = $request->headers;

        return $this->login($loginRequest);
    }

    /**
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
     * Remove the specified resource from storage.
     */
    public function refresh(): Response
    {
        return $this->response($this->respondWithToken(auth()->refresh()));
    }
}
