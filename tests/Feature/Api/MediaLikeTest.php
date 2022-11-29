<?php

namespace Aparlay\Core\Tests\Feature\Api;

use Aparlay\Core\Api\V1\Models\Block;
use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Models\Enums\MediaStatus;
use Aparlay\Core\Models\Enums\MediaVisibility;
use Aparlay\Core\Models\MediaLike;
use Aparlay\Core\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use MongoDB\BSON\ObjectId;

class MediaLikeTest extends ApiTestCase
{
    /**
     * @test
     */
    public function likeMedia()
    {
        $likeCreator = User::factory()->create();
        $mediaCreator = User::factory()->create(['like_count' => 0]);
        $media = \Aparlay\Core\Models\Media::factory()->for($mediaCreator, 'userObj')->create([
            'is_protected' => false,
            'created_by' => $mediaCreator->_id,
            'like_count' => 0,
            'status' => MediaStatus::COMPLETED->value,
            'visibility' => MediaVisibility::PUBLIC->value,
            'creator' => [
                '_id' => $mediaCreator->_id,
                'username' => $mediaCreator->username,
                'avatar' => $mediaCreator->avatar,
            ],
        ]);
        $this->assertEquals(0, $media->like_count);

        $this->assertDatabaseMissing((new MediaLike())->getCollection(), ['media_id' => new ObjectId($media->_id)]);

        $this->actingAs($likeCreator)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->json('PUT', '/v1/media/'.$media->_id.'/like', [])
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
                    'media_id',
                    'user_id',
                ],
            ])->assertJson(
                fn (AssertableJson $json) => $json->whereAllType([
                    'code' => 'integer',
                    'uuid' => 'string',
                    'status' => 'string',
                    'data._id' => 'string',
                    'data.created_at' => 'integer',
                    'data.media_id' => 'string',
                    'data.user_id' => 'string',
                    'data.creator' => 'array',
                    'data.creator._id' => 'string',
                    'data.creator.username' => 'string',
                    'data.creator.avatar' => 'string',
                    'data.creator.is_followed' => 'boolean',
                ])
            );

        $this->assertDatabaseHas((new MediaLike())->getCollection(), [
            'creator._id' => new ObjectId($likeCreator->_id),
            'media_id' => new ObjectId($media->_id),
        ]);

        $this->assertDatabaseHas((new Media())->getCollection(), [
            '_id' => $media->_id,
            'like_count' => 1,
        ]);

        $this->assertDatabaseHas((new User())->getCollection(), [
            '_id' => $mediaCreator->_id,
            'like_count' => 1,
        ]);
    }

    /**
     * @test
     */
    public function unlikeMedia()
    {
        $user = User::factory()->create();
        $mediaCreator = User::factory()->create();
        $media = Media::factory()->for($mediaCreator, 'userObj')
            ->create(['status' => MediaStatus::COMPLETED->value, 'visibility' => MediaVisibility::PUBLIC->value]);

        $this->actingAs($user)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->json('PUT', '/v1/media/'.$media->_id.'/like', [])
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
                    'media_id',
                    'user_id',
                ],
            ]);

        $this->assertDatabaseHas('media_likes', [
            'created_by' => new ObjectId($user->_id),
            'media_id' => new ObjectId($media->_id),
        ]);

        $likeCountOfMedia = $media->like_count + 1;
        $likeCountOfUser = $mediaCreator->like_count + 1;

        $this->actingAs($user)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->json('DELETE', '/v1/media/'.$media->_id.'/like', [])
            ->assertStatus(204);
        $this->assertDatabaseMissing('media_likes', [
            'created_by' => new ObjectId($user->_id),
            'media_id' => new ObjectId($media->_id),
        ]);

        $this->assertEquals($likeCountOfMedia - 1, $media->like_count);
        $this->assertEquals($likeCountOfUser - 1, $mediaCreator->like_count);
    }

    /**
     * @test
     */
    public function mediaLikePermission()
    {
        $mediaCreator = User::factory()->create();
        $blockedUser = User::factory()->create();
        $block = Block::factory()->create([
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
            'status' => MediaStatus::COMPLETED->value,
            'visibility' => MediaVisibility::PUBLIC->value,
            'created_by' => $mediaCreator->_id,
            'creator' => [
                '_id' => $mediaCreator->_id,
                'username' => $mediaCreator->username,
                'avatar' => $mediaCreator->avatar,
            ],
        ]);
        $this->actingAs($blockedUser)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->json('PUT', '/v1/media/'.$media->_id.'/like', [])
            ->assertStatus(403)
            ->assertJson([
                'code' => 403,
                'status' => 'ERROR',
                'data' => [],
                'message' => 'You cannot like this video at the moment.',
            ]);
    }
}
