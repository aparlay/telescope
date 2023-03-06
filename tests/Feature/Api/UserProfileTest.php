<?php

namespace Aparlay\Core\Tests\Feature\Api;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Models\Enums\MediaVisibility;
use Aparlay\Core\Models\Enums\UserStatus;
use Aparlay\Core\Models\Enums\UserVisibility;
use Illuminate\Testing\Fluent\AssertableJson;
use MongoDB\BSON\ObjectId;

class UserProfileTest extends ApiTestCase
{
    /**
     * A basic unit test for username invalid.
     *
     * @test
     */
    public function invalidUsername()
    {
        $user = User::factory()->create(['status' => UserStatus::VERIFIED->value]);
        $this->actingAs($user)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->postJson('/v1/me?_method=PATCH', ['username' => 'a'])
            ->assertStatus(422)
            ->assertJson([
                'code' => 422,
                'status' => 'ERROR',
                'data' => [
                    [
                        'field' => 'username',
                        'message' => 'The username must be at least 3 characters.',
                    ],
                ],
                'message' => 'There are some errors in your provided data.',
            ]);
    }

    /**
     * A basic unit test for username already exist.
     *
     * @test
     */
    public function usernameExist()
    {
        User::factory()->create(['username' => 'alua_user']);
        $user = User::factory()->create(['status' => UserStatus::VERIFIED->value]);
        $this->actingAs($user)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->postJson('/v1/me?_method=PATCH', ['username' => 'alua_user'])
            ->assertStatus(422)
            ->assertJson([
                'code' => 422,
                'status' => 'ERROR',
                'data' => [
                    [
                        'field' => 'username',
                        'message' => 'The username has already been taken.',
                    ],
                ],
                'message' => 'There are some errors in your provided data.',
            ]);
    }

    /**
     * A basic unit test for avatar mime type.
     *
     * @test
     */
    public function invalidAvatarExtension()
    {
        $user = User::factory()->create(['status' => UserStatus::ACTIVE->value]);
        $this->actingAs($user)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->postJson('/v1/me?_method=PATCH', ['avatar' => 'demo_image.doc'])
            ->assertStatus(422)
            ->assertJson([
                'code' => 422,
                'status' => 'ERROR',
                'data' => [
                    [
                        'field' => 'avatar',
                        'message' => 'The avatar must be an image. The avatar must be a file of type: png, jpg, jpeg, gif.',
                    ],
                ],
                'message' => 'There are some errors in your provided data.',
            ]);
    }

    /**
     * Get user profile.
     *
     * @test
     */
    public function userProfileById()
    {
        $user = User::factory()->create(['status' => UserStatus::ACTIVE->value]);
        $userDeactivated = User::factory()->create(['status' => UserStatus::DEACTIVATED->value]);
        $userViewer = User::factory()->create(['status' => UserStatus::ACTIVE->value]);

        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->get('/v1/user/'.$user->_id)
            ->assertStatus(200)
            ->assertJsonPath('status', 'OK')
            ->assertJsonPath('code', 200)
            ->assertJsonStructure(
                [
                    'data' => [
                        '_id',
                        'username',
                        'bio',
                        'full_name',
                        'avatar',
                        'visibility',
                        'is_followed',
                        'is_blocked',
                        'promo_link',
                        'follower_count',
                        'following_count',
                        'like_count',
                        'created_at',
                        'updated_at',
                        '_links' => [
                            'self' => ['href'],
                        ],
                    ],
                ]
            )->assertJson(
                fn (AssertableJson $json) => $json->whereAllType([
                    'code' => 'integer',
                    'uuid' => 'string',
                    'status' => 'string',
                    'data._id' => 'string',
                    'data.username' => 'string',
                    'data.bio' => 'string',
                    'data.full_name' => 'string',
                    'data.avatar' => 'string',
                    'data.visibility' => 'integer',
                    'data.is_followed' => 'boolean',
                    'data.is_blocked' => 'boolean',
                    'data.promo_link' => 'null|string',
                    'data.follower_count' => 'integer',
                    'data.following_count' => 'integer',
                    'data.like_count' => 'integer',
                    'data.created_at' => 'integer',
                    'data.updated_at' => 'integer',
                    'data._links' => 'array',
                    'data._links.self' => 'array',
                    'data._links.self.href' => 'string',
                ])
            );

        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->get('/v1/user/'.$userDeactivated->_id)
            ->assertStatus(423);

        $this->actingAs($userViewer)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->get('/v1/user/'.$user->_id)
            ->assertStatus(200)
            ->assertJsonPath('status', 'OK')
            ->assertJsonPath('code', 200)
            ->assertJsonStructure(
                [
                    'data' => [
                        '_id',
                        'username',
                        'bio',
                        'full_name',
                        'avatar',
                        'visibility',
                        'is_followed',
                        'is_blocked',
                        'promo_link',
                        'follower_count',
                        'following_count',
                        'like_count',
                        'created_at',
                        'updated_at',
                        '_links' => [
                            'self' => ['href'],
                        ],
                    ],
                ]
            )->assertJson(
                fn (AssertableJson $json) => $json->whereAllType([
                    'code' => 'integer',
                    'status' => 'string',
                    'uuid' => 'string',
                    'data._id' => 'string',
                    'data.username' => 'string',
                    'data.bio' => 'string',
                    'data.full_name' => 'string',
                    'data.avatar' => 'string',
                    'data.visibility' => 'integer',
                    'data.is_followed' => 'boolean',
                    'data.is_blocked' => 'boolean',
                    'data.promo_link' => 'null|string',
                    'data.follower_count' => 'integer',
                    'data.following_count' => 'integer',
                    'data.like_count' => 'integer',
                    'data.created_at' => 'integer',
                    'data.updated_at' => 'integer',
                    'data._links' => 'array',
                    'data._links.self' => 'array',
                    'data._links.self.href' => 'string',
                ])
            );
    }

    /**
     * Get user profile.
     *
     * @test
     */
    public function userProfileByUsername()
    {
        $user = User::factory()->create(['status' => UserStatus::ACTIVE->value]);
        $userDeactivated = User::factory()->create(['status' => UserStatus::DEACTIVATED->value]);
        $userViewer = User::factory()->create(['status' => UserStatus::ACTIVE->value]);

        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->get('/v1/user/share/'.$user->username)
            ->assertStatus(200)
            ->assertJsonPath('status', 'OK')
            ->assertJsonPath('code', 200)
            ->assertJsonStructure(
                [
                    'data' => [
                        '_id',
                        'username',
                        'bio',
                        'full_name',
                        'avatar',
                        'visibility',
                        'is_followed',
                        'is_blocked',
                        'promo_link',
                        'follower_count',
                        'following_count',
                        'like_count',
                        'created_at',
                        'updated_at',
                        '_links' => [
                            'self' => ['href'],
                        ],
                    ],
                ]
            )->assertJson(
                fn (AssertableJson $json) => $json->whereAllType([
                    'code' => 'integer',
                    'uuid' => 'string',
                    'status' => 'string',
                    'data._id' => 'string',
                    'data.username' => 'string',
                    'data.bio' => 'string',
                    'data.full_name' => 'string',
                    'data.avatar' => 'string',
                    'data.visibility' => 'integer',
                    'data.is_followed' => 'boolean',
                    'data.is_blocked' => 'boolean',
                    'data.promo_link' => 'null|string',
                    'data.follower_count' => 'integer',
                    'data.following_count' => 'integer',
                    'data.like_count' => 'integer',
                    'data.created_at' => 'integer',
                    'data.updated_at' => 'integer',
                    'data._links' => 'array',
                    'data._links.self' => 'array',
                    'data._links.self.href' => 'string',
                ])
            );

        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->get('/v1/user/'.$userDeactivated->_id)
            ->assertStatus(423);

        $this->actingAs($userViewer)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->get('/v1/user/'.$user->_id)
            ->assertStatus(200)
            ->assertJsonPath('status', 'OK')
            ->assertJsonPath('code', 200)
            ->assertJsonStructure(
                [
                    'data' => [
                        '_id',
                        'username',
                        'bio',
                        'full_name',
                        'avatar',
                        'visibility',
                        'is_followed',
                        'is_blocked',
                        'promo_link',
                        'follower_count',
                        'following_count',
                        'like_count',
                        'created_at',
                        'updated_at',
                        '_links' => [
                            'self' => ['href'],
                        ],
                    ],
                ]
            )->assertJson(
                fn (AssertableJson $json) => $json->whereAllType([
                    'code' => 'integer',
                    'status' => 'string',
                    'uuid' => 'string',
                    'data._id' => 'string',
                    'data.username' => 'string',
                    'data.bio' => 'string',
                    'data.full_name' => 'string',
                    'data.avatar' => 'string',
                    'data.visibility' => 'integer',
                    'data.is_followed' => 'boolean',
                    'data.is_blocked' => 'boolean',
                    'data.promo_link' => 'null|string',
                    'data.follower_count' => 'integer',
                    'data.following_count' => 'integer',
                    'data.like_count' => 'integer',
                    'data.created_at' => 'integer',
                    'data.updated_at' => 'integer',
                    'data._links' => 'array',
                    'data._links.self' => 'array',
                    'data._links.self.href' => 'string',
                ])
            );
    }

    /**
     * delete user account.
     *
     * @test
     */
    public function deleteAccount()
    {
        $user = User::factory()->create(['status' => UserStatus::ACTIVE->value, 'password' => 'password']);

        $oldEmail = $user->email;
        $oldPhoneNumber = $user->phone_number;

        $user = \Aparlay\Core\Models\User::factory()->create(['password' => 'password']);
        $credentials = ['username' => $user->username, 'password' => 'password'];
        $token = auth()->attempt($credentials);

        $this->actingAs($user)
            ->withHeaders([
                'Authorization' => 'Bearer '.$token,
                'X-DEVICE-ID' => 'random-string',
            ])
            ->json('POST', '/v1/me/delete', [])
            ->assertStatus(204);

        $userDetails = User::where('_id', new ObjectId($user->_id))->first();

        $this->assertDatabaseHas('users', ['_id' => new ObjectId($userDetails->_id)]);
        $this->assertNotEquals($oldEmail, $userDetails->email);
        $this->assertNotEquals($oldPhoneNumber, $userDetails->phone_number);
    }

    /**
     * get user account details.
     *
     * @test
     */
    public function getUserDetails()
    {
        $user = User::factory()->create(['status' => UserStatus::ACTIVE->value]);

        $r = $this->actingAs($user)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->get('/v1/me', []);

        $r->assertStatus(200)
            ->assertJsonPath('status', 'OK')
            ->assertJsonPath('code', 200)
            ->assertJsonStructure([
                'data' => [
                    '_id',
                    'username',
                    'bio',
                    'full_name',
                    'email',
                    'email_verified',
                    'phone_number',
                    'phone_number_verified',
                    'avatar',
                    'setting' => [
                        'otp',
                        'show_adult_content',
                        'filter_content_gender' => [
                            'female',
                            'male',
                            'transgender',
                        ],
                        'notifications' => [
                            'unread_message_alerts',
                            'news_and_updates',
                            'new_followers',
                            'new_subscribers',
                            'tips',
                            'likes',
                            'comments',
                        ],
                    ],
                    'features' => [
                        'tips',
                        'demo',
                    ],
                    'gender',
                    'status',
                    'visibility',
                    'promo_link',
                    'follower_count',
                    'following_count',
                    'like_count',
                    'block_count',
                    'followed_hashtag_count',
                    'media_count',
                    'is_followed',
                    'is_blocked',
                    'blocks' => [],
                    'likes' => [],
                    'followers' => [],
                    'followings' => [],
                    'medias' => [],
                    'alerts' => [],
                    'created_at',
                    'updated_at',
                    '_links' => [
                        'self' => ['href'],
                    ],
                    'tags' => [],
                ],
            ])->assertJson(
                fn (AssertableJson $json) => $json->whereAllType([
                    'code' => 'integer',
                    'status' => 'string',
                    'uuid' => 'string',
                    'data._id' => 'string',
                    'data.referral_id' => 'string',
                    'data.username' => 'string',
                    'data.full_name' => 'string',
                    'data.email' => 'string',
                    'data.email_verified' => 'boolean',
                    'data.phone_number' => 'string',
                    'data.phone_number_verified' => 'boolean',
                    'data.status' => 'integer',
                    'data.bio' => 'string',
                    'data.avatar' => 'string',
                    'data.setting' => 'array',
                    'data.setting.otp' => 'boolean',
                    'data.setting.show_adult_content' => 'integer',
                    'data.setting.filter_content_gender' => 'array',
                    'data.setting.filter_content_gender.female' => 'boolean',
                    'data.setting.filter_content_gender.male' => 'boolean',
                    'data.setting.filter_content_gender.transgender' => 'boolean',
                    'data.setting.notifications' => 'array',
                    'data.setting.notifications.unread_message_alerts' => 'boolean',
                    'data.setting.notifications.new_followers' => 'boolean',
                    'data.setting.notifications.news_and_updates' => 'boolean',
                    'data.setting.notifications.tips' => 'boolean',
                    'data.setting.notifications.likes' => 'boolean',
                    'data.setting.notifications.comments' => 'boolean',
                    'data.setting.notifications.new_subscribers' => 'boolean',
                    'data.features' => 'array',
                    'data.features.tips' => 'boolean',
                    'data.features.demo' => 'boolean',
                    'data.gender' => 'integer',
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
                    'data.alerts' => 'array',
                    'data.created_at' => 'integer',
                    'data.updated_at' => 'integer',
                    'data._links' => 'array',
                    'data._links.self' => 'array',
                    'data._links.self.href' => 'string',
                    'data.tags' => 'array',
                    'message' => 'string',
                ])
            );
    }

    /**
     * A basic unit test for username invalid.
     *
     * @test
     */
    public function updateUserVisibility()
    {
        $user = User::first();
        $user->update(['visibility' => UserVisibility::PUBLIC->value]);
        $user->refresh();

        Media::factory()->for($user, 'userObj')
            ->create([
                'visibility' => MediaVisibility::PUBLIC->value,
                'creator' => [
                    '_id' => new ObjectId($user->_id),
                    'username' => $user->username,
                    'avatar' => $user->avatar,
                ],
            ]);

        $r = $this->actingAs($user)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->postJson('/v1/me?_method=PUT', [
                'visibility' => UserVisibility::PRIVATE->value,
            ]);

        $r->assertStatus(200)
            ->assertJsonStructure(
                [
                    'data' => [
                        '_id',
                        'username',
                        'bio',
                        'full_name',
                        'email',
                        'email_verified',
                        'phone_number',
                        'phone_number_verified',
                        'avatar',
                        'setting' => [
                            'otp',
                            'show_adult_content',
                            'filter_content_gender' => [
                                'female',
                                'male',
                                'transgender',
                            ],
                            'notifications' => [
                                'unread_message_alerts',
                                'news_and_updates',
                                'new_followers',
                                'new_subscribers',
                                'tips',
                                'likes',
                                'comments',
                            ],
                        ],
                        'features' => [
                            'tips',
                            'demo',
                        ],
                        'gender',
                        'status',
                        'visibility',
                        'promo_link',
                        'follower_count',
                        'following_count',
                        'like_count',
                        'block_count',
                        'followed_hashtag_count',
                        'media_count',
                        'is_followed',
                        'is_blocked',
                        'blocks' => [],
                        'likes' => [],
                        'followers' => [],
                        'followings' => [],
                        'medias' => [],
                        'alerts' => [],
                        'created_at',
                        'updated_at',
                        '_links' => [
                            'self' => ['href'],
                        ],
                        'tags' => [],
                    ],
                ]
            )
            ->assertJson(
                fn (AssertableJson $json) => $json->whereAllType([
                    'code' => 'integer',
                    'uuid' => 'string',
                    'status' => 'string',
                    'data._id' => 'string',
                    'data.username' => 'string',
                    'data.bio' => 'string',
                    'data.full_name' => 'string',
                    'data.avatar' => 'string',
                    'data.visibility' => 'integer',
                    'data.is_followed' => 'boolean',
                    'data.is_blocked' => 'boolean',
                    'data.promo_link' => 'null|string',
                    'data.follower_count' => 'integer',
                    'data.following_count' => 'integer',
                    'data.like_count' => 'integer',
                    'data.created_at' => 'integer',
                    'data.updated_at' => 'integer',
                    'data._links' => 'array',
                    'data._links.self' => 'array',
                    'data._links.self.href' => 'string',
                    'data.tags' => 'array',
                    'message' => 'string',
                ])
            );

        $userDetails = User::where('_id', new ObjectId($user->_id))->first();
        $this->assertEquals(UserVisibility::PRIVATE->value, $userDetails->visibility);
    }

    /**
     * A basic unit test for username invalid.
     *
     * @test
     */
    public function updateInvisibleUserVisibility()
    {
        $user = User::first();
        $user->update(['visibility' => UserVisibility::INVISIBLE_BY_ADMIN->value]);
        $user->refresh();

        $r = $this->actingAs($user)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->postJson('/v1/me?_method=PUT', [
                'visibility' => UserVisibility::PRIVATE->value,
            ]);

        $r->assertStatus(422)
            ->assertJson([
                'code' => 422,
                'status' => 'ERROR',
                'data' => [
                    [
                        'field' => 'visibility',
                        'message' => 'Your account is invisible by administrator, you cannot change it to public/private.',
                    ],
                ],
                'message' => 'There are some errors in your provided data.',
            ]);

        $userDetails = User::where('_id', new ObjectId($user->_id))->first();
        $this->assertEquals(UserVisibility::INVISIBLE_BY_ADMIN->value, $userDetails->visibility);
    }
}
