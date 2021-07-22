<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\Block;
use Aparlay\Core\Api\V1\Models\User;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
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
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Block  $media
     * @return Response
     */
    public function token(Block $media)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Block  $media
     * @return Response
     */
    public function changePassword(Block $media)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Block  $media
     * @return Response
     */
    public function validateOtp(Block $media)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Block  $media
     * @return Response
     */
    public function requestOtp(Block $media)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request  $request
     * @return Response
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
            return response()->json(['error' => $validator->errors()], 401);
        }

        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->response($this->respondWithToken($token));
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
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
            return response()->json(['error' => $validator->errors()], 401);
        }

        $user = new User();
        $user->email = $request->email;
        $user->password_hash = Hash::make($request->password);
        $user->username = ($request->username) ? $request->username : null;
        $user->phone_number = ($request->phone_number) ? $request->phone_number : null;
        $user->gender = $request->gender;
        $user->status = User::STATUS_PENDING;
        $user->visibility = User::VISIBILITY_PUBLIC;
        $user->save();

        return response()->json([
            'success' => true,
            'data' => $user,
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Block  $media
     * @return Response
     */
    public function logout(Block $media)
    {
        auth()->logout();

        return $this->response(['message' => 'Success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Block  $media
     * @return Response
     */
    public function refresh(Block $media)
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ];
    }
}
