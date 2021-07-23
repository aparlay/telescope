<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Validator;

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
    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required',
            ]
        );

        if ($validator->fails()) {
            return $this->error(
                __('The given data was invalid.'),
                $validator->errors()->toArray(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        if (!$token = auth()->attempt($validator->validated())) {
            return $this->error(
                __('Data Validation Failed'),
                $validator->errors()->toArray(),
                Response::HTTP_UNAUTHORIZED
            );
        }

        return $this->response($this->respondWithToken($token));
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
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ];
    }

    /**
     * Register a User.
     *
     * @return JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email|unique:users|max:100',
                'password' => 'required|min:8|max:20',
                'gender' => 'required|numeric',
                'username' => 'nullable|min:6|max:20',
                'phone_number' => 'nullable|numeric',
            ]
        );

        if ($validator->fails()) {
            return $this->error(
                __('Data Validation Failed'),
                $validator->errors()->toArray(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $user = new User();
        $user->email = $request->email;
        $user->password_hash = Hash::make($request->password);
        $user->username = ($request->username) ?: null;
        $user->phone_number = ($request->phone_number) ?: null;
        $user->gender = $request->gender;
        $user->status = User::STATUS_PENDING;
        $user->visibility = User::VISIBILITY_PUBLIC;
        $user->save();

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
