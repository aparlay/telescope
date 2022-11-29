<?php

namespace Aparlay\Core\Tests\Feature\Api;

use Aparlay\Core\Models\Enums\UserStatus;
use Aparlay\Core\Models\Otp;
use Aparlay\Core\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

class ValidateOtpTest extends ApiTestCase
{
    /**
     * A test for valid otp.
     *
     * @test
     */
    public function validOtp()
    {
        $email = uniqid('alua_').'@aparlay.com';
        $otp = '123456';

        User::factory()->create([
            'status' => UserStatus::ACTIVE->value,
            'email' => $email,
        ]);
        Otp::factory()->create(['identity' => $email, 'otp' => $otp, 'validated' => false]);
        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->patchJson('/v1/validate-otp', [
                'email' => $email,
                'otp' => $otp,
            ])
            ->assertStatus(200)
            ->assertJson([
                'code' => 200,
                'status' => 'OK',
                'data' => [
                    'message' => 'OTP is matched with your Email',
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

    /**
     * A test for invalid otp.
     *
     * @test
     */
    public function invalidOtp()
    {
        $user = User::factory()->create([
            'status' => UserStatus::ACTIVE->value,
            'email' => uniqid('alua_').'@aparlay.com',
        ]);
        Otp::factory()->create(['identity' => $user->email, 'otp' => '123456']);
        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->patchJson('/v1/validate-otp', [
                'email' => $user->email,
                'otp' => '112233',
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

    /**
     * A test for require email.
     *
     * @test
     */
    public function emailRequire()
    {
        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->patchJson('/v1/validate-otp', ['email' => '', 'otp' => '123456'])
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
                'message' => 'There are some errors in your provided data.',
            ]);
    }

    /**
     * A test for require otp.
     *
     * @test
     */
    public function otpRequire()
    {
        $user = User::factory()->create([
            'status' => UserStatus::ACTIVE->value,
            'email' => uniqid('alua_').'@aparlay.com',
        ]);
        $response = $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->patchJson('/v1/validate-otp', ['email' => $user->email, 'otp' => ''])
            ->assertStatus(422)
            ->assertJson([
                'code' => 422,
                'status' => 'ERROR',
                'data' => [
                    [
                        'field' => 'otp',
                        'message' => 'The otp field is required.',
                    ],
                ],
                'message' => 'There are some errors in your provided data.',
            ]);
    }
}
