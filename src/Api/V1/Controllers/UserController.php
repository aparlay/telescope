<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\Block;
use Aparlay\Core\Api\V1\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;

class UserController extends Controller
{
    public $token = true;

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return $this->response([], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Block  $media
     * @return Response
     */
    public function me(Block $media): Response
    {
        return $this->response([], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Block  $media
     * @return Response
     */
    public function destroy(Block $media): Response
    {
        return $this->response([], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Block  $media
     * @return Response
     */
    public function update(Block $media): Response
    {
        return $this->response([], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function register(Request $request): Response
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $this->error('validation failed', ['error' => $validator->errors()], Response::HTTP_UNAUTHORIZED);
        }

        $user = new User();
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        if ($this->token) {
            return $this->login($request);
        }

        return $this->response($user,'',  Response::HTTP_OK);
    }
}
