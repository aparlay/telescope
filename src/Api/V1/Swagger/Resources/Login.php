<?php

namespace Aparlay\Core\Api\V1\Swagger\Resources;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema()
 */
class Login
{
    /**
     * @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vYXBpLmFsdWE1NTUuY29tL3YxL2xvZ2luIiwiaWF0IjoxNjgxODA0MDU0LCJleHAiOjE2ODIwMjAwNTQsIm5iZiI6MTY4MTgwNDA1NCwianRpIjoiN1AxakpTVGNVcHM1b0JYOCIsInN1YiI6IjY0M2U0YWU1OWEwNmQyZGM4MjBjY2EzMiIsInBydiI6ImMyOTQxN2E1ZDU2MjAxYzI0OWU0ODk5MjA1ODA1NDhjMWQxMGQyMWYiLCJkZXZpY2VfaWQiOiIxIn0.cUfsbTZRFUnLExSaKnwHVCckKRfMVXUaAEQGfQrKo")
     */
    public $token;

    /**
     * @OA\Property(property="token_expired_at", type="int", example=216000)
     */
    public $token_expired_at;

    /**
     * @OA\Property(property="refresh_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vYXBpLmFsdWE1NTUuY29tL3YxL2xvZ2luIiwiaWF0IjoxNjgxODA0MDU0LCJleHAiOjE2ODIwMjAwNTQsIm5iZiI6MTY4MTgwNDA1NCwianRpIjoiN1AxakpTVGNVcHM1b0JYOCIsInN1YiI6IjY0M2U0YWU1OWEwNmQyZGM4MjBjY2EzMiIsInBydiI6ImMyOTQxN2E1ZDU2MjAxYzI0OWU0ODk5MjA1ODA1NDhjMWQxMGQyMWYiLCJkZXZpY2VfaWQiOiIxIn0.cUfsbTZRFUnLExSaKnwHVCckKRfMVXUaAEQGfQrKo")
     */
    public $refresh_token;

    /**
     * @OA\Property(property="refresh_token_expired_at", type="int", example=216000)
     */
    public $refresh_token_expired_at;
}
