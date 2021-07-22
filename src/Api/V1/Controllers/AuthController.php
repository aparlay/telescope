<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\Block;
use Illuminate\Http\Request;

use Illuminate\Http\Response;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
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
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->response($this->respondWithToken($token));
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
