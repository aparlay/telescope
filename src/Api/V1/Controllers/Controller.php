<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     description="Waptap is creating an elegant web/mobile solution to share adult media (video/photo) among a network of your followers.",
 *     version="1.0.0",
 *     title="Waptap Api",
 *     termsOfService="https://www.waptap.com/terms/",
 *     @OA\Contact(
 *         email="info@waptap.com"
 *     ),
 *     @OA\License(
 *         name="Apache 2.0",
 *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 * @OA\Tag(
 *     name="user",
 *     description="Operations about user",
 *     @OA\ExternalDocumentation(
 *         description="Find out more about store",
 *         url="http://swagger.io"
 *     )
 * )
 * @OA\Server(
 *     description="SwaggerHUB API Mocking",
 *     url="https://api.waptap.dev"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 * )
 *
 * @OA\Schema(
 *      schema="201",
 *      required={"message", "status", "code", "data"},
 *      @OA\Property(
 *          property="message",
 *          type="string",
 *          example="Entity has been created successfully!"
 *      ),
 *      @OA\Property(
 *          property="status",
 *          type="string",
 *          format="string",
 *          example="OK"
 *      ),
 *      @OA\Property(
 *          property="code",
 *          type="integer",
 *          format="int32",
 *          example=201
 *      )
 *  )
 *
 * @OA\Schema(
 *      schema="400",
 *      required={"name", "message", "status", "code"},
 *      @OA\Property(
 *          property="name",
 *          format="string",
 *          example="Bad Request"
 *      ),
 *      @OA\Property(
 *          property="message",
 *          type="string",
 *          example="Invalid chunk uploaded."
 *      ),
 *      @OA\Property(
 *          property="status",
 *          type="string",
 *          format="string",
 *          example="ERROR"
 *      ),
 *      @OA\Property(
 *          property="code",
 *          type="integer",
 *          format="int32",
 *          example=401
 *      ),
 *      @OA\Property(
 *          property="items",
 *          type="array",
 *          format="array",
 *          @OA\Items (
 *              @OA\Property(
 *                  property="field",
 *                  type="string",
 *                  example="username"
 *              ),
 *              @OA\Property(
 *                  property="message",
 *                  type="string",
 *                  example="Username is not a valid email address."
 *              )
 *          )
 *      )
 *  )
 *
 * @OA\Schema(
 *      schema="401",
 *      required={"name", "message", "status", "code"},
 *      @OA\Property(
 *          property="name",
 *          format="string",
 *          example="Unauthorized"
 *      ),
 *      @OA\Property(
 *          property="message",
 *          type="string",
 *          example="Unable to load and verify the token."
 *      ),
 *      @OA\Property(
 *          property="status",
 *          type="string",
 *          format="string",
 *          example="ERROR"
 *      ),
 *      @OA\Property(
 *          property="code",
 *          type="integer",
 *          format="int32",
 *          example=401
 *      ),
 *      @OA\Property(
 *          property="items",
 *          type="array",
 *          format="array",
 *          @OA\Items (
 *              @OA\Property(
 *                  property="field",
 *                  type="string",
 *                  example="username"
 *              ),
 *              @OA\Property(
 *                  property="message",
 *                  type="string",
 *                  example="Username is not a valid email address."
 *              )
 *          )
 *      )
 *  )
 *
 * @OA\Schema(
 *      schema="418",
 *      required={"message", "status", "code", "data"},
 *      @OA\Property(
 *          property="message",
 *          type="string",
 *          example="OTP has been sent."
 *      ),
 *      @OA\Property(
 *          property="status",
 *          type="string",
 *          format="string",
 *          example="ERROR"
 *      ),
 *      @OA\Property(
 *          property="code",
 *          type="integer",
 *          format="int32",
 *          example=418
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="object",
 *          @OA\Property(
 *               property="message",
 *               type="string",
 *               example="OTP has been sent to your email."
 *           )
 *      )
 *  )
 *
 * @OA\Schema(
 *      schema="422",
 *      required={"name", "message", "status", "code", "items"},
 *      @OA\Property(
 *          property="name",
 *          format="string",
 *          example="Data Validation Failed"
 *      ),
 *      @OA\Property(
 *          property="message",
 *          type="string",
 *          example="There are some errors in your provided data."
 *      ),
 *      @OA\Property(
 *          property="status",
 *          type="string",
 *          format="string",
 *          example="ERROR"
 *      ),
 *      @OA\Property(
 *          property="code",
 *          type="integer",
 *          format="int32",
 *          example=422
 *      ),
 *      @OA\Property(
 *          property="items",
 *          type="array",
 *          format="array",
 *          @OA\Items (
 *              @OA\Property(
 *                  property="field",
 *                  type="string",
 *                  example="username"
 *              ),
 *              @OA\Property(
 *                  property="message",
 *                  type="string",
 *                  example="Username is not a valid email address."
 *              )
 *          )
 *      )
 *  )
 *
 * @OA\Schema(
 *      schema="423",
 *      required={"name", "message", "status", "code"},
 *      @OA\Property(
 *          property="name",
 *          format="string",
 *          example="Locked"
 *      ),
 *      @OA\Property(
 *          property="message",
 *          type="string",
 *          example="You cannot create more OTP, please wait a while to receive an otp or try again later."
 *      ),
 *      @OA\Property(
 *          property="status",
 *          type="string",
 *          format="string",
 *          example="ERROR"
 *      ),
 *      @OA\Property(
 *          property="code",
 *          type="integer",
 *          format="int32",
 *          example=422
 *      )
 *  )
 * @OA\Schema(
 *      schema="429",
 *      required={"name", "message", "status", "code"},
 *      @OA\Property(
 *          property="name",
 *          format="string",
 *          example="Too Many Requests"
 *      ),
 *      @OA\Property(
 *          property="message",
 *          type="string",
 *          example="Rate limit exceeded."
 *      ),
 *      @OA\Property(
 *          property="status",
 *          type="string",
 *          format="string",
 *          example="ERROR"
 *      ),
 *      @OA\Property(
 *          property="code",
 *          type="integer",
 *          format="int32",
 *          example=429
 *      )
 *  )
 */
class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    public $repository;

    /**
     * success response method.
     *
     * @param $result
     * @param  string  $message
     * @param  int  $code
     * @param  array  $headers
     * @return Response
     */
    public function response($result, string $message = '', int $code = 200, array $headers = []): Response
    {
        $response = [
            'code' => $code,
            'status' => 'OK',
            'data' => $result,
        ];

        if (! empty($message)) {
            $response['message'] = $message;
        }

        return response($response, $code, $headers);
    }

    /**
     * return error response.
     *
     * @param $error
     * @param  array  $errorMessages
     * @param  int  $code
     * @param  array  $headers
     * @return Response
     */
    public function error($error, array $errorMessages = [], int $code = 400, array $headers = []): Response
    {
        $response = [
            'code' => $code,
            'status' => 'ERROR',
            'message' => $error,
        ];

        if (! empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response($response, $code, $headers);
    }
}
