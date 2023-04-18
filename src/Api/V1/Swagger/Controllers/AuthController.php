<?php

namespace Aparlay\Core\Api\V1\Swagger\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Put(
 *     path="/v1/change-password",
 *     tags={"Core | User"},
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
 * @OA\Patch (
 *     path="/v1/validate-otp",
 *     tags={"Core | User"},
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
 * @OA\Post(
 *     path="/v1/request-otp",
 *     tags={"Core | User"},
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
 * @OA\Post(
 *     path="/v1/login",
 *     tags={"Core | User"},
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
 *         @OA\JsonContent(
 *             @OA\Property(property="code", format="integer", example=200),
 *             @OA\Property(property="status", format="string", example="OK"),
 *             @OA\Property(property="uuid", format="string", example="1"),
 *             @OA\Property(property="data", ref="#/components/schemas/Login")
 *
 *         ),
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
 * @OA\Post(
 *     path="/v1/register",
 *     tags={"Core | User"},
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
 * @OA\Delete (
 *     path="/v1/logout",
 *     tags={"Core | User"},
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
 * @OA\Put(
 *     path="/v1/refresh-token",
 *     tags={"Core | User"},
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
 */
class AuthController
{
}
