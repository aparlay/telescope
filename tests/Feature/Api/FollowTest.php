<?php

namespace Aparlay\Core\Tests\Feature\Api;

use Aparlay\Core\Api\V1\Models\Block;
use Aparlay\Core\Api\V1\Models\Follow;
use Aparlay\Core\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use MongoDB\BSON\ObjectId;

class FollowTest extends ApiTestCase
{
    /**
     * @test
     */
    public function follow_user()
    {
        $model = User::factory()->create();
        $user  = User::factory()->create();
        $this->actingAs($user)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->json('PUT', '/v1/user/' . $model->_id . '/follow', [])
            ->assertStatus(201)
            ->assertJsonPath('status', 'OK')
            ->assertJsonPath('code', 201)
            ->assertJsonStructure([
                'data' => [
                    '_id',
                    'created_at',
                    'creator' => [
                        '_id',
                        'username',
                        'avatar',
                        'is_followed',
                    ],
                    'user' => [
                        '_id',
                        'username',
                        'avatar',
                        'is_followed',
                    ],
                ],
            ])->assertJson(
                fn (AssertableJson $json) => $json->whereAllType([
                    'code' => 'integer',
                    'status' => 'string',
                    'uuid' => 'string',
                    'data._id' => 'string',
                    'data.created_at' => 'integer',
                    'data.creator' => 'array',
                    'data.creator._id' => 'string',
                    'data.creator.username' => 'string',
                    'data.creator.avatar' => 'string',
                    'data.creator.is_followed' => 'boolean',
                    'data.user' => 'array',
                    'data.user._id' => 'string',
                    'data.user.username' => 'string',
                    'data.user.avatar' => 'string',
                    'data.user.is_followed' => 'boolean',
                ])
            );

        $this->assertDatabaseHas('user_follows', ['user._id' => new ObjectId($model->_id), 'creator._id' => new ObjectId($user->_id)]);
    }

    /**
     * @test
     */
    public function unfollow_user()
    {
        $model  = User::factory()->create();
        $user   = User::factory()->create();
        $follow = Follow::factory()->create([
            'user' => [
                '_id' => new ObjectId($model->_id),
                'username' => $model->username,
                'avatar' => $model->avatar,
            ],
            'creator' => [
                '_id' => new ObjectId($user->_id),
                'username' => $user->username,
                'avatar' => $user->avatar,
            ],
        ]);
        $this->actingAs($user)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->json('DELETE', '/v1/user/' . $follow->user['_id'] . '/follow', [])
            ->assertStatus(204);

        $this->assertDatabaseMissing('user_follows', ['user._id' => new ObjectId($model->_id), 'creator._id' => new ObjectId($user->_id)]);
    }

    /**
     * @test
     */
    public function follow_user_with_permission()
    {
        $user        = User::factory()->create();
        $blockedUser = User::factory()->create();
        $block       = Block::factory()->create([
            'user' => [
                '_id' => new ObjectId($blockedUser->_id),
                'username' => $blockedUser->username,
                'avatar' => $blockedUser->avatar,
            ],
            'creator' => [
                '_id' => new ObjectId($user->_id),
                'username' => $user->username,
                'avatar' => $user->avatar,
            ],
        ]);
        $this->actingAs($blockedUser)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->json('PUT', '/v1/user/' . $user->_id . '/follow', [])
            ->assertStatus(403)
            ->assertJson([
                'code' => 403,
                'status' => 'ERROR',
                'data' => [],
                'message' => 'You cannot follow this user at the moment.',
            ]);
    }
}
