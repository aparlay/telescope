<?php

namespace Aparlay\Core\Tests\Feature\Api;

use Aparlay\Core\Models\Enums\UserStatus;
use Aparlay\Core\Models\Otp;
use Aparlay\Core\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

class RequestOtpTest extends ApiTestCase
{
    /**
     * A test for valid request otp.
     *
     * @test
     */
    public function validRequestOtp()
    {
        $user = User::factory()->create([
            'status' => UserStatus::ACTIVE->value,
            'email' => uniqid('alua_').'@aparlay.com',
        ]);
        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->postJson('/v1/request-otp', ['email' => $user->email])
            ->assertStatus(200)
            ->assertJson([
                'code' => 200,
                'status' => 'OK',
                'data' => [
                    'message' => 'If you enter your email correctly you will receive an OTP email in your inbox soon.',
                ],
            ])->assertJson(
                fn (AssertableJson $json) => $json->whereAllType([
                    'code' => 'integer',
                    'status' => 'string',
                    'uuid' => 'string',
                    'data.message' => 'string',
                ])
            );

        $this->assertDatabaseHas('otps', ['identity' => $user->email]);
    }

    /**
     * A test for require email.
     *
     * @test
     */
    public function requireEmailInRequestOtp()
    {
        $user = User::factory()->create([
            'status' => UserStatus::ACTIVE->value,
            'email' => uniqid('alua_').'@aparlay.com',
        ]);
        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->postJson('/v1/request-otp', ['email' => ''])
            ->assertStatus(422)
            ->assertJson([
                'code' => 422,
                'status' => 'ERROR',
                'data' => [
                    [
                        'field' => 'email',
                        'message' => 'The email field is required.',
                    ],
                ],
            ]);
    }

    /**
     * A test for exit otp limit.
     *
     * @test
     */
    public function exitOtpLimit()
    {
        $user = User::factory()->create([
            'status' => UserStatus::ACTIVE->value,
            'email' => uniqid('alua_').'@aparlay.com',
        ]);

        for ($x = 1; $x <= 5; $x++) {
            Otp::factory()->create(['identity' => $user->email]);
        }

        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->postJson('/v1/request-otp', ['email' => $user->email])
            ->assertStatus(423)
            ->assertJsonFragment([
                'code' => 423,
                'status' => 'ERROR',
                'data' => [],
            ]);
    }

    /**
     * A test for user not exist.
     *
     * @test
     */
    public function userNotExistForRequestOtp()
    {
        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->postJson('/v1/request-otp', ['email' => uniqid('alua_').'@aparlay.com'])

            ->assertStatus(200)
            ->assertJson([
                'code' => 200,
                'status' => 'OK',
                'data' => [
                    'message' => 'If you enter your email correctly you will receive an OTP email in your inbox soon.',
                ],
            ])->assertJson(
                fn (AssertableJson $json) => $json->whereAllType([
                    'code' => 'integer',
                    'uuid' => 'string',
                    'status' => 'string',
                    'data.message' => 'string',
                ])
            );
    }
}
