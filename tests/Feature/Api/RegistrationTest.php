<?php

namespace Aparlay\Core\Tests\Feature\Api;

use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Models\Enums\UserStatus;
use Illuminate\Testing\Fluent\AssertableJson;

class RegistrationTest extends ApiTestCase
{
    /**
     * A basic unit test for valid user registration.
     *
     * @test
     */
    public function valid_register()
    {
        $payload = [
            'email' => uniqid('alua_') . '@aparlay.com',
            'password' => 'password@123',
            'gender' => 1,
        ];

        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->postJson('/v1/register', $payload)
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
                    'uuid' => 'string',
                    'status' => 'string',
                    'data.token' => 'string',
                    'data.token_expired_at' => 'integer',
                    'data.refresh_token' => 'string',
                    'data.refresh_token_expired_at' => 'integer',
                ])
            );
        $this->assertDatabaseHas('users', ['email' => $payload['email']]);
    }

    /**
     * A basic unit test for using username as email.
     *
     * @test
     */
    public function username_as_email()
    {
        $referrer = User::factory()->create(['status' => UserStatus::ACTIVE->value]);

        $payload  = [
            'username' => uniqid('alua_') . '@apaly.com',
            'password' => 'Demo@123',
            'gender' => 0,
            'referral_id' => $referrer->_id,
        ];

        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->postJson('/v1/register', $payload)
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
                    'uuid' => 'string',
                    'status' => 'string',
                    'data.token' => 'string',
                    'data.token_expired_at' => 'integer',
                    'data.refresh_token' => 'string',
                    'data.refresh_token_expired_at' => 'integer',
                ])
            );

        $this->assertDatabaseHas('users', ['email' => $payload['username']]);
    }

    /**
     * A basic unit test for invalid password.
     *
     * @test
     */
    public function invalid_password()
    {
        $referrer = User::factory()->create(['status' => UserStatus::ACTIVE->value]);

        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->postJson('/v1/register', [
                'email' => uniqid('alua_') . '@aparlay.com',
                'password' => 'Demo@',
                'gender' => 0,
                'referral_id' => $referrer->_id,
            ])->assertStatus(422)
            ->assertJson([
                'code' => 422,
                'status' => 'ERROR',
                'data' => [
                    [
                        'field' => 'password',
                        'message' => 'The password must be at least 8 characters. The password must contain at least one number.',
                    ],
                ],
                'message' => 'There are some errors in your provided data.',
            ]);
    }

    /**
     * A basic unit test for password is require.
     *
     * @test
     */
    public function password_require()
    {
        $referrer = User::factory()->create(['status' => UserStatus::ACTIVE->value]);

        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->postJson('/v1/register', [
                'email' => uniqid('alua_') . '@aparlay.com',
                'password' => '',
                'gender' => 0,
                'referral_id' => $referrer->_id,
            ])->assertStatus(422)
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
     * A basic unit test for email not valid .
     *
     * @test
     */
    public function email_not_valid()
    {
        $referrer = User::factory()->create(['status' => UserStatus::ACTIVE->value]);

        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->postJson('/v1/register', [
                'email' => 'aparlay105',
                'password' => 'Demo@123',
                'gender' => 0,
                'referral_id' => $referrer->_id,
            ])->assertStatus(422)
            ->assertJson([
                'code' => 422,
                'status' => 'ERROR',
                'data' => [
                    [
                        'field' => 'email',
                        'message' => 'The email must be a valid email address.',
                    ],
                ],
                'message' => 'There are some errors in your provided data.',
            ]);
    }

    /**
     * A basic unit test for gender not valid .
     *
     * @test
     */
    public function gender_not_valid()
    {
        $referrer = User::factory()->create(['status' => UserStatus::ACTIVE->value]);

        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->postJson('/v1/register', [
                'email' => uniqid('alua_') . '@aparlay.com',
                'password' => 'Demo@123',
                'gender' => 5,
                'referral_id' => $referrer->_id,
            ])->assertStatus(422)
            ->assertJson([
                'code' => 422,
                'status' => 'ERROR',
                'data' => [
                    [
                        'field' => 'gender',
                        'message' => 'The selected gender is invalid.',
                    ],
                ],
                'message' => 'There are some errors in your provided data.',
            ]);
    }

    /**
     * A basic unit test for invalid phone_number with digits.
     *
     * @test
     */
    public function invalid_phone_number_with_digits()
    {
        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->postJson('/v1/register', [
                'email' => uniqid('alua_') . '@aparlay.com',
                'password' => 'Demo@123',
                'gender' => 2,
                'phone_number' => '12345',
            ])->assertStatus(422)
            ->assertJson([
                'code' => 422,
                'status' => 'ERROR',
                'data' => [
                    [
                        'field' => 'phone_number',
                        'message' => 'The phone number must be 10 digits.',
                    ],
                ],
                'message' => 'There are some errors in your provided data.',
            ]);
    }

    /**
     * A basic unit test for invalid phone_number validation.
     *
     * @test
     */
    public function phone_number_invalid()
    {
        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->postJson('/v1/register', [
                'email' => uniqid('alua_') . '@gmail.com',
                'password' => 'Demo@123',
                'gender' => 2,
                'phone_number' => 'asdasd',
            ])->assertStatus(422)
            ->assertJson([
                'code' => 422,
                'status' => 'ERROR',
                'data' => [
                    [
                        'field' => 'phone_number',
                        'message' => 'The phone number must be a number. The phone number must be 10 digits.',
                    ],
                ],
                'message' => 'There are some errors in your provided data.',
            ]);
    }
}
