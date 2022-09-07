<?php

namespace Aparlay\Core\Tests\Feature\Api;

use Aparlay\Core\Models\Enums\UserStatus;
use Aparlay\Core\Models\Otp;
use Aparlay\Core\Models\User;
use Illuminate\Support\Facades\Hash;

class ChangePasswordTest extends ApiTestCase
{
    /**
     * A test, change password for user not found.
     *
     * @test
     */
    public function userNotFound()
    {
        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->json('PUT', '/v1/change-password', [
                'old_password' => 'User@12345',
                'password' => 'Demo@demo12',
            ])
            ->assertStatus(404)
            ->assertJson([
                'code' => 404,
                'status' => 'ERROR',
                'data' => [],
                'message' => 'User not found',
            ]);
    }

    /**
     * A test for change password and user unverified.
     *
     * @test
     */
    public function changePasswordWithUnverifiedUser()
    {
        $user = User::factory()->create([
            'status' => UserStatus::PENDING->value,
            'email' => uniqid('alua+').'@aparlay.com',
            'password_hash' => Hash::make('Demo@12345'),
        ]);
        $this->actingAs($user)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->putJson('/v1/change-password', [
                'old_password' => 'Demo@12345',
                'password' => 'Demo@demo12',
            ])
            ->assertStatus(422)
            ->assertJson([
                'code' => 422,
                'status' => 'ERROR',
                'data' => [
                    [
                        'field' => 'Account',
                        'message' => 'Your account is not authenticated.',
                    ],
                ],
                'message' => 'There are some errors in your provided data.',
            ]);
    }

    /**
     * A policy test for change password and user not found.
     *
     * @test
     */
    public function changePasswordLoginUser()
    {
        $user = User::factory()->create([
            'status' => UserStatus::ACTIVE->value,
            'email' => uniqid('alua+').'@aparlay.com',
            'password_hash' => Hash::make('Demo@12345'),
        ]);

        $this->actingAs($user)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->putJson('/v1/change-password', [
                'old_password' => 'Demo@12345',
                'password' => 'Demo@demo12',
            ])
            ->assertStatus(200)
            ->assertJson([
                'code' => 200,
                'status' => 'OK',
                'data' => [],
        ]);
        $user->refresh();
        $this->assertFalse(Hash::check('Demo@12345', $user->password_hash));
        $this->assertTrue(Hash::check('Demo@demo12', $user->password_hash));
    }

    /**
     * A test for change password for not login user.
     *
     * @test
     */
    public function changePasswordWithoutLoginUser()
    {
        $user = User::factory()->create([
            'status' => UserStatus::ACTIVE->value,
            'email' => uniqid('alua+').'@aparlay.com',
        ]);
        $otp = Otp::factory()->create([
            'identity' => $user->email,
            'otp' => '123456', 'validated' => true,
        ]);
        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->putJson('/v1/change-password', [
                'email' => $user->email,
                'otp' => $otp->otp,
                'password' => 'Any@12345',
            ])
            ->assertStatus(200)
            ->assertJson([
                'code' => 200,
                'status' => 'OK',
                'data' => [],
            ]);
        $user->refresh();
        $this->assertTrue(Hash::check('Any@12345', $user->password_hash));
    }

    /**
     * A test for require old password.
     *
     * @test
     */
    public function loginUserOldPassowordRequire()
    {
        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->putJson('/v1/change-password', ['old_password' => '', 'password' => 'demo13215'])
            ->assertStatus(422)
            ->assertJson([
                'code' => 422,
                'status' => 'ERROR',
                'data' => [
                    [
                        'field' => 'email',
                        'message' => 'The email field is required when old password is not present.',
                    ],
                    [
                        'field' => 'otp',
                        'message' => 'The otp field is required when old password is not present.',
                    ],
                    [
                        'field' => 'old_password',
                        'message' => 'The old password field is required when email is not present.',
                    ],
                ],
                'message' => 'There are some errors in your provided data.',
            ]);
    }

    /**
     * A test for require password.
     *
     * @test
     */
    public function loginUserNewPassowordRequire()
    {
        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->putJson('/v1/change-password', ['old_password' => 'Any@12345', 'password' => ''])
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
     * A test for require email.
     *
     * @test
     */
    public function emailRequireWhenNonLoginUser()
    {
        $response = $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->putJson('/v1/change-password', [
                'password' => 'Demo132415',
                'email' => '',
                'otp' => '123456',
            ])
            ->assertStatus(422)
            ->assertJson([
                'code' => 422,
                'status' => 'ERROR',
                'data' => [
                    [
                        'field' => 'email',
                        'message' => 'The email must be a valid email address. The email field is required when old password is not present.',
                    ],
                    [
                        'field' => 'old_password',
                        'message' => 'The old password field is required when email is not present.',
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
    public function otpRequireWhenNonLoginUser()
    {
        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->putJson('/v1/change-password', [
                'password' => 'Any@12345',
                'email' => uniqid('alua+').'@aparlay.com',
                'otp' => '',
            ])
            ->assertStatus(422)
            ->assertJson([
                'code' => 422,
                'status' => 'ERROR',
                'data' => [
                    [
                        'field' => 'otp',
                        'message' => 'The otp field is required when old password is not present.',
                    ],
                ],
                'message' => 'There are some errors in your provided data.',
            ]);
    }

    /**
     * A test for require password for not login user.
     *
     * @test
     */
    public function passwordRequireWhenNonLoginUser()
    {
        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->putJson('/v1/change-password', [
                'password' => '',
                'email' => uniqid('alua+').'@aparlay.com',
                'otp' => '123456',
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
     * A test for in-validate Otp with not login user.
     *
     * @test
     */
    public function invalidOtpWhenNonLoginUser()
    {
        $user = User::factory()->create([
            'status' => UserStatus::ACTIVE->value,
            'email' => uniqid('alua+').'@aparlay.com',
        ]);
        Otp::factory()->create([
            'identity' => $user->email,
            'otp' => '123456',
        ]);
        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->putJson('/v1/change-password', [
                'email' => $user->email,
                'password' => 'Any@12345',
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
}
