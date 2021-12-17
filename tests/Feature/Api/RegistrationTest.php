<?php

namespace Aparlay\Core\Tests\Feature\Api;

use Aparlay\Core\Api\V1\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

class RegistrationTest extends ApiTestCase
{
    /**
     * A basic unit test for valid user registration.
     *
     * @test
     */
    public function validRegister()
    {
        $payload = [
            'email' => uniqid('alua_').'@aparlay.com',
            'password' => 'password@123',
            'gender' => 1,
        ];

        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->postJson('/v1/register', $payload)
            ->assertStatus(201)
            ->assertJsonPath('status', 'OK')
            ->assertJsonPath('code', 201)
            ->assertJsonStructure([
                'data' => [
                    '_id',
                    'referral_id',
                    'username',
                    'full_name',
                    'email',
                    'email_verified',
                    'phone_number',
                    'phone_number_verified',
                    'status',
                    'bio',
                    'avatar',
                    'setting' => [
                        'otp',
                        'notifications' => [
                            'unread_message_alerts',
                            'new_followers',
                            'news_and_updates',
                            'tips',
                            'new_subscribers',
                        ],
                    ],
                    'features' => [
                        'tips',
                        'demo',
                    ],
                    'gender',
                    'interested_in',
                    'visibility',
                    'block_count',
                    'follower_count',
                    'following_count',
                    'like_count',
                    'followed_hashtag_count',
                    'media_count',
                    'is_followed',
                    'is_blocked',
                    'promo_link',
                    'blocks',
                    'likes',
                    'followers',
                    'followings',
                    'medias',
                    'created_at',
                    'updated_at',
                    '_links' => [
                        'self' => [
                            'href',
                        ],
                    ],
                ],
                'message',
            ])->assertJson(
                fn (AssertableJson $json) => $json->whereAllType([
                    'code' => 'integer',
                    'status' => 'string',
                    'data._id' => 'string',
                    'data.referral_id' => 'string',
                    'data.username' => 'string',
                    'data.full_name' => 'null|string',
                    'data.email' => 'string',
                    'data.email_verified' => 'boolean',
                    'data.phone_number' => 'null|string',
                    'data.phone_number_verified' => 'boolean',
                    'data.status' => 'integer',
                    'data.bio' => 'null|string',
                    'data.avatar' => 'string',
                    'data.setting' => 'array',
                    'data.setting.otp' => 'boolean',
                    'data.setting.notifications' => 'array',
                    'data.setting.notifications.unread_message_alerts' => 'boolean',
                    'data.setting.notifications.new_followers' => 'boolean',
                    'data.setting.notifications.news_and_updates' => 'boolean',
                    'data.setting.notifications.tips' => 'boolean',
                    'data.setting.notifications.new_subscribers' => 'boolean',
                    'data.features' => 'array',
                    'data.features.tips' => 'boolean',
                    'data.features.demo' => 'boolean',
                    'data.gender' => 'integer',
                    'data.interested_in' => 'integer',
                    'data.visibility' => 'integer',
                    'data.block_count' => 'integer',
                    'data.follower_count' => 'integer',
                    'data.following_count' => 'integer',
                    'data.like_count' => 'integer',
                    'data.followed_hashtag_count' => 'integer',
                    'data.media_count' => 'integer',
                    'data.is_followed' => 'boolean',
                    'data.is_blocked' => 'boolean',
                    'data.promo_link' => 'null|string',
                    'data.blocks' => 'array',
                    'data.likes' => 'array',
                    'data.followers' => 'array',
                    'data.followings' => 'array',
                    'data.medias' => 'array',
                    'data.created_at' => 'integer',
                    'data.updated_at' => 'integer',
                    'data._links' => 'array',
                    'data._links.self' => 'array',
                    'data._links.self.href' => 'string',
                    'message' => 'string',
                ])
            );
        $this->assertDatabaseHas('users', ['email' => $payload['email']]);
    }

    /**
     * A basic unit test for using username as email.
     *
     * @test
     */
    public function usernameAsEmail()
    {
        $referrer = User::factory()->create(['status' => User::STATUS_ACTIVE]);

        $payload = [
            'username' => uniqid('alua_').'@apaly.com',
            'password' => 'Demo@123',
            'gender' => 0,
            'referral_id' => $referrer->_id,
        ];

        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->postJson('/v1/register', $payload)
            ->assertStatus(201)
            ->assertJson([
                'code' => 201,
                'status' => 'OK',
                'data' => [],
                'message' => 'Entity has been created successfully!',
            ]);

        $this->assertDatabaseHas('users', ['email' => $payload['username']]);
    }

    /**
     * A basic unit test for invalid password.
     *
     * @test
     */
    public function invalidPassword()
    {
        $referrer = User::factory()->create(['status' => User::STATUS_ACTIVE]);

        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->postJson('/v1/register', [
                'email' => uniqid('alua_').'@aparlay.com',
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
    public function passwordRequire()
    {
        $referrer = User::factory()->create(['status' => User::STATUS_ACTIVE]);

        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->postJson('/v1/register', [
                'email' => uniqid('alua_').'@aparlay.com',
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
    public function emailNotValid()
    {
        $referrer = User::factory()->create(['status' => User::STATUS_ACTIVE]);

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
    public function genderNotValid()
    {
        $referrer = User::factory()->create(['status' => User::STATUS_ACTIVE]);

        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->postJson('/v1/register', [
                'email' => uniqid('alua_').'@aparlay.com',
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
    public function invalidPhoneNumberWithDigits()
    {
        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->postJson('/v1/register', [
                'email' => uniqid('alua_').'@aparlay.com',
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
    public function phoneNumberInvalid()
    {
        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->postJson('/v1/register', [
                'email' => uniqid('alua_').'@gmail.com',
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
