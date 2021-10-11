<?php

namespace Aparlay\Core\Tests\Feature\Api;

use Aparlay\Core\Api\V1\Models\Block;
use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\Report;
use Aparlay\Core\Api\V1\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use MongoDB\BSON\ObjectId;

class ReportTest extends ApiTestCase
{
    /**
     * @test
     */
    public function reasonRequired()
    {
        $user = User::factory()->create();
        $modal = User::factory()->create();
        $this->actingAs($modal)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->json('POST', '/v1/user/'.$user->_id.'/report', ['reason' => ''])
            ->assertStatus(422)
            ->assertJson([
                'code' => 422,
                'status' => 'ERROR',
                'data' => [
                    [
                        'field' => 'reason',
                        'message' => 'The reason field is required.',
                    ],
                ],
                'message' => 'There are some errors in your provided data.',
            ]);
    }

    /**
     * @test
     */
    public function reportUser()
    {
        $user = User::factory()->create(['status' => User::STATUS_ACTIVE]);
        $modal = User::factory()->create(['status' => User::STATUS_ACTIVE]);
        $this->actingAs($modal)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->json('POST', '/v1/user/'.$user->_id.'/report', ['reason' => 'Reason Test Case'])
            ->assertStatus(201)
            ->assertJsonPath('status', 'OK')
            ->assertJsonPath('code', 201)
            ->assertJsonStructure([
                'data' => [
                    'reason',
                    'type',
                    'status',
                    'user_id',
                    'media_id',
                    'updated_at',
                    'created_at',
                    '_id',
                ],
            ])->assertJson(
                fn (AssertableJson $json) => $json->whereAllType([
                    'code' => 'integer',
                    'status' => 'string',
                    'data._id' => 'string',
                    'data.media_id' => 'string',
                    'data.user_id' => 'string',
                    'data.comment_id' => 'string',
                    'data.reason' => 'string',
                    'data.type' => 'string',
                    'data.status' => 'string',
                    'data.created_at' => 'integer',
                    'data.updated_at' => 'integer',
                ])
            );

        $this->assertDatabaseHas('reports', ['user_id' => new ObjectId($user->_id)]);
    }

    /**
     * @test
     */
    public function guestReportMedia()
    {
        $media = \Aparlay\Core\Models\Media::factory()->for(\Aparlay\Core\Models\User::factory()->create(), 'userObj')->create();
        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->json('POST', '/v1/media/'.$media->_id.'/report', [
                'reason' => 'bad image for guest',
            ])
            ->assertStatus(201)
            ->assertJsonPath('status', 'OK')
            ->assertJsonPath('code', 201)
            ->assertJsonStructure([
                'data' => [
                    'reason',
                    'type',
                    'status',
                    'media_id',
                    'user_id',
                    'updated_at',
                    'created_at',
                    '_id',
                ],
            ])->assertJson(
                fn (AssertableJson $json) => $json->whereAllType([
                    'code' => 'integer',
                    'status' => 'string',
                    'data._id' => 'string',
                    'data.media_id' => 'string',
                    'data.user_id' => 'string',
                    'data.comment_id' => 'string',
                    'data.reason' => 'string',
                    'data.type' => 'string',
                    'data.status' => 'string',
                    'data.created_at' => 'integer',
                    'data.updated_at' => 'integer',
                ])
            );

        $this->assertDatabaseHas('reports', ['media_id' => new ObjectId($media->_id), 'type' => Report::TYPE_MEDIA]);
    }

    /**
     * @test
     */
    public function userReportMedia()
    {
        $user = User::factory()->create();
        $media = Media::factory()->for(User::factory()->create(), 'userObj')->create();
        $this->actingAs($user)->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->json('POST', '/v1/media/'.$media->_id.'/report', [
                'reason' => 'bad image',
            ])
            ->assertStatus(201)
            ->assertJsonPath('status', 'OK')
            ->assertJsonPath('code', 201)
            ->assertJsonStructure([
                'data' => [
                    'reason',
                    'type',
                    'status',
                    'user_id',
                    'media_id',
                    'updated_at',
                    'created_at',
                    '_id',
                ],
            ])->assertJson(
                fn (AssertableJson $json) => $json->whereAllType([
                    'code' => 'integer',
                    'status' => 'string',
                    'data._id' => 'string',
                    'data.media_id' => 'string',
                    'data.user_id' => 'string',
                    'data.comment_id' => 'string',
                    'data.reason' => 'string',
                    'data.type' => 'string',
                    'data.status' => 'string',
                    'data.created_at' => 'integer',
                    'data.updated_at' => 'integer',
                ])
            );
        $this->assertDatabaseHas('reports', ['media_id' => new ObjectId($media->_id), 'created_by' => new ObjectId($user->_id), 'type' => Report::TYPE_MEDIA]);
    }

    /**
     * @test
     */
    public function reportUserWithPermission()
    {
        $user = User::factory()->create();
        $blockedUser = User::factory()->create();
        Block::factory()->create([
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
            ->json('POST', '/v1/user/'.$user->_id.'/report', ['reason' => 'Test Case For Policy'])
            ->assertStatus(403)
            ->assertJson([
                'code' => 403,
                'status' => 'ERROR',
                'data' => [],
                'message' => 'You cannot report this user at the moment.',
            ]);
    }

    /**
     * @test
     */
    public function mediaReportWithPermission()
    {
        $mediaCreator = User::factory()->create();
        $blockedUser = User::factory()->create();
        Block::factory()->create([
            'user' => [
                '_id' => new ObjectId($blockedUser->_id),
                'username' => $blockedUser->username,
                'avatar' => $blockedUser->avatar,
            ],
            'creator' => [
                '_id' => new ObjectId($mediaCreator->_id),
                'username' => $mediaCreator->username,
                'avatar' => $mediaCreator->avatar,
            ],
        ]);
        $media = Media::factory()->for($mediaCreator, 'userObj')->create([
            'is_protected' => true,
            'created_by' => $mediaCreator->_id,
            'creator' => [
                '_id' => $mediaCreator->_id,
                'username' => $mediaCreator->username,
                'avatar' => $mediaCreator->avatar,
            ],
        ]);
        $this->actingAs($blockedUser)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->json('POST', '/v1/media/'.$media->_id.'/report', ['reason' => 'Test Case For Policy'])
            ->assertStatus(403)
            ->assertJson([
                'code' => 403,
                'status' => 'ERROR',
                'data' => [],
                'message' => 'You cannot report this video at the moment.',
            ]);
    }
}