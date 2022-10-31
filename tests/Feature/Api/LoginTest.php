<?php

namespace Aparlay\Core\Tests\Feature\Api;

use Aparlay\Core\Models\Enums\UserStatus;
use Aparlay\Core\Models\Otp;
use Aparlay\Core\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

class LoginTest extends ApiTestCase
{
    /**
     * A basic unit for valid login.
     *
     * @test
     */
    public function validLogin()
    {
        $activeUser = User::factory()->create([
            'email' => uniqid('alua_').'@aparly.com',
            'status' => UserStatus::ACTIVE->value,
            'password' => 'password',
            'settings' => ['otp' => false],
        ]);

        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->postJson('/v1/login', [
                'username' => $activeUser->email,
                'password' => 'password',
            ])
            ->assertStatus(200)
            ->assertJsonPath('status', 'OK')
            ->assertJsonPath('code', 200)
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'token_expired_at',
                    'refresh_token',
                    'refresh_token_expired_at',
                ],
            ])->assertJson(
                fn (AssertableJson $json) => $json->whereAllType([
                    'code' => 'integer',
                    'status' => 'string',
                    'data.token' => 'string',
                    'data.token_expired_at' => 'integer',
                    'data.refresh_token' => 'string',
                    'data.refresh_token_expired_at' => 'integer',
                ])
            );
    }

    /**
     * A basic unit test for invalid email.
     *
     * @test
     */
    public function invalidEmail()
    {
        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->postJson('/v1/login', ['username' => uniqid('alua_').'@aparly.com', 'password' => 'Demo@12345'])
            ->assertStatus(422)
            ->assertJson([
                'code' => 422,
                'status' => 'ERROR',
                'data' => [
                    [
                        'field' => 'password',
                        'message' => 'Incorrect username or password.',
                    ],
                ],
                'message' => 'There are some errors in your provided data.',
            ]);
    }

    /**
     * A basic unit test for require username.
     *
     * @test
     */
    public function requireUsername()
    {
        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->postJson('/v1/login', ['username' => '', 'password' => 'Demo12345'])
            ->assertStatus(422)
            ->assertJson([
                'code' => 422,
                'status' => 'ERROR',
                'data' => [
                    [
                        'field' => 'username',
                        'message' => 'The username field is required.',
                    ],
                ],
                'message' => 'There are some errors in your provided data.',
            ]);
    }

    /**
     * A basic unit test for require password.
     *
     * @test
     */
    public function requirePassword()
    {
        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->postJson('/v1/login', [
                'username' => uniqid('alua_').'@aparly.com',
                'password' => '',
            ])
            ->assertStatus(422)
            ->assertJson([
                'code' => 422,
                'status' => 'ERROR',
                'data' => [
                    [
                        'field' => 'password',
                        'message' => 'The password field is required.',
                    ],
                ],
                'message' => 'There are some errors in your provided data.',
            ]);
    }

    /**
     * A basic unit test for request otp.
     *
     * @test
     * @TODO this test is wrong this must apply some changes in code
     *       to support require OTP in settings
     */
    public function requestOtp()
    {
        $user = User::factory()->create([
            'status' => UserStatus::PENDING->value,
            'setting' => [
                'otp' => true,
            ],
        ]);
        $response = $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->postJson('/v1/login', [
                'username' => $user->email,
                'password' => 'password',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'OK')
            ->assertJsonPath('code', 200)
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'token_expired_at',
                    'refresh_token',
                    'refresh_token_expired_at',
                ],
            ])->assertJson(
                fn (AssertableJson $json) => $json->whereAllType([
                    'code' => 'integer',
                    'status' => 'string',
                    'data.token' => 'string',
                    'data.token_expired_at' => 'integer',
                    'data.refresh_token' => 'string',
                    'data.refresh_token_expired_at' => 'integer',
                ])
            );
    }

    /**
     * A basic unit test for validate Otp with login.
     *
     * @test
     */
    public function validateOtpWithLogin()
    {
        $user = User::factory()->create([
            'status' => UserStatus::PENDING->value,
            'setting' => [
                'otp' => true,
            ],
        ]);
        $otp = Otp::factory()->create([
            'identity' => $user->email,
            'incorrect' => 0,
            'validated' => false,
            'created_by' => $user,
            'updated_by' => $user,
        ]);

        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->postJson('/v1/login', [
                'username' => $user->email,
                'password' => 'password',
                'otp' => $otp->otp,
            ])
            ->assertStatus(200)
            ->assertJsonPath('status', 'OK')
            ->assertJsonPath('code', 200)
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'token_expired_at',
                    'refresh_token',
                    'refresh_token_expired_at',
                ],
            ])->assertJson(
                fn (AssertableJson $json) => $json->whereAllType([
                    'code' => 'integer',
                    'status' => 'string',
                    'data.token' => 'string',
                    'data.token_expired_at' => 'integer',
                    'data.refresh_token' => 'string',
                    'data.refresh_token_expired_at' => 'integer',
                ])
            );
    }

    /**
     * A basic unit test for in-validate Otp with login.
     *
     * @test
     */
    public function inValidateOtpWithLogin()
    {
        $user = User::factory()->create([
            'status' => UserStatus::PENDING->value,
            'setting' => [
                'otp' => true,
            ],
        ]);
        $otp = Otp::factory()->create([
            'identity' => $user->email,
            'incorrect' => 0,
            'validated' => false,
            'created_by' => $user,
            'updated_by' => $user,
        ]);
        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->postJson('/v1/login', [
                'username' => $user->email,
                'password' => 'password',
                'otp' => '592018',
            ])
            ->assertStatus(422)
            ->assertJson([
                'code' => 422,
                'status' => 'ERROR',
                'data' => [
                    [
                        'field' => 'otp',
                        'message' => 'Invalid Code.',
                    ],
                ],
                'message' => 'There are some errors in your provided data.',
            ]);
    }
}
