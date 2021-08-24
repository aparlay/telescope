<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Requests\ChangePasswordRequest;
use Aparlay\Core\Api\V1\Requests\LoginRequest;
use Aparlay\Core\Api\V1\Requests\RegisterRequest;
use Aparlay\Core\Api\V1\Requests\RequestOtpRequest;
use Aparlay\Core\Api\V1\Requests\ValidateOtpRequest;
use Aparlay\Core\Api\V1\Resources\RegisterResource;
use Aparlay\Core\Models\Login;
use Aparlay\Core\Repositories\UserRepository;
use Aparlay\Core\Services\OtpService;
use Aparlay\Core\Services\UserService;
use App\Exceptions\BlockedException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Gate;
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
        $this->middleware('auth:api', [
            'except' => [
                'login',
                'register',
                'requestOtp',
                'validateOtp',
                'changePassword'
            ]
        ]);
        $this->repository = new UserRepository(new User());
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
     * Change password.
     *
     * @param ChangePasswordRequest $request
     * @return Response
     */
    public function changePassword(ChangePasswordRequest $request): Response
    {
        /* Change password scenario */
        if ($request->old_password) {
            $user = auth()->user();

            /** Check the update permission */
            $response = Gate::inspect('update', $user);
            if (! $response->allowed()) {
                throw new BlockedException($response->message(), null, null, Response::HTTP_FORBIDDEN);
            }

            /* Change the password in database table */
            $this->repository->resetPassword($request->password, $user);
        } else {
            /* Forgot password scenario */
            if (! ($user = UserService::findByIdentity($request->username))) {
                throw new BlockedException('User not found', null, null, Response::HTTP_NOT_FOUND);
            }

            /* Validate the OTP or Throw exception if OTP is incorrect */
            OtpService::validateOtp($request->otp, $request->username, false, true);

            /* verifying user if status is pending */
            if ($this->repository->isUnverified($user)) {
                $this->repository->verify($user);
            }

            /* Store the new password in database */
            $this->repository->resetPassword($request->password, $user);
        }

        return $this->login(new LoginRequest(['password' => $request->password, 'username' => $user->username]));
    }

    /**
     * Validate request OTP.
     *
     * @param ValidateOtpRequest $request
     * @return Response
     */
    public function validateOtp(ValidateOtpRequest $request): Response
    {
        /* Find the user based on username */
        if (! ($user = UserService::findByIdentity($request->username))) {
            throw new BlockedException('User not found', null, null, Response::HTTP_NOT_FOUND);
        }

        /* Through exception for suspended/banned/NotFound accounts */
        $this->repository->isUserEligible($user);

        /* Validate the OTP or Throw exception if OTP is incorrect */
        OtpService::validateOtp($request->otp, $request->username, true);

        /* Find the identityField (Email/Phone Number/Username) based on username and return the response*/
        return $this->response([
            'message' => 'OTP is matched with your '.ucfirst(str_replace('_', ' ', UserService::getIdentityType($request->username))),
        ], '', Response::HTTP_OK);
    }

    /**
     * Request for otp.
     *
     * @param RequestOtpRequest $request
     * @return Response
     */
    public function requestOtp(RequestOtpRequest $request): Response
    {
        /* Find the user based on username */
        if (! ($user = UserService::findByIdentity($request->username))) {
            throw new BlockedException('User not found', null, null, Response::HTTP_NOT_FOUND);
        }

        /* Through exception for suspended/banned/NotFound accounts */
        $this->repository->isUserEligible($user);

        // Send the OTP or Throw exception if send OTP limit is reached
        OtpService::sendOtp($user, $request->headers->get('X-DEVICE-ID'));

        /* Find the identityField (Email/PhoneNumber/Username) based on username and return the response*/
        if (UserService::getIdentityType($request->username) === Login::IDENTITY_EMAIL) {
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
        if (! ($token = auth()->attempt($credentials))) {
            throw ValidationException::withMessages(['password' => ['Incorrect username or password.']]);
        }

        /** Through exception for suspended/banned/NotFound accounts */
        $user = auth()->user();
        $this->repository->isUserEligible($user);
        $deviceId = $request->headers->get('X-DEVICE-ID');

        if ($this->repository->isUnverified($user)) {
            if ($request->otp) {
                OtpService::validateOtp($request->otp, $request->username);
                $this->repository->verify($user);
            } else {
                OtpService::sendOtp($user, $deviceId);
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
                throw new BlockedException('OTP has been sent.', null, null, Response::HTTP_LOCKED, $response);
            }
        }

        /** Prepare and return the json response */
        $result = $this->respondWithToken($token);
        $cookie1 = Cookie::make(
            '__Secure_token',
            $result['access_token'],
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
        $deviceId = $request->headers->get('X-DEVICE-ID');
        $identity = $user->phone_number ?? $user->email;

        /** Find the identityField (Email/PhoneNumber/Username) based on username */
        $identityField = UserService::getIdentityType($identity);
        if ($this->repository->isUnverified($user)) {
            OtpService::sendOtp($user, $deviceId);
        }

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
