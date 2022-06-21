<?php

namespace Aparlay\Core\Tests\Feature\Api;

use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\MediaComment;
use Aparlay\Core\Models\MediaCommentLike;
use Aparlay\Core\Models\Report;
use Aparlay\Core\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use MongoDB\BSON\ObjectId;

class MediaCommentTest extends ApiTestCase
{
    const MEDIA_COMMENT_TYPES = [
        'code' => 'integer',
        'status' => 'string',
        'data._id' => 'string',
        'data.created_at' => 'integer',
        'data.likes_count' => 'integer',
        'data.replies_count' => 'integer',
        'data.user_id' => 'string',
        'data.creator' => 'array',
        'data.creator._id' => 'string',
        'data.creator.username' => 'string',
        'data.creator.avatar' => 'string',
    ];

    const MEDIA_COMMENT_REPLY_STRUCTURE =
        [
            '_id',
            'created_at',
            'parent_id',
            'text',
            'creator' => [
                '_id',
                'username',
                'avatar',
            ],
            'is_liked',
            'media_id',
            'user_id',
        ]
    ;


    const MEDIA_COMMENT_STRUCTURE = [
        '_id',
        'created_at',
        'parent_id',
        'text',
        'creator' => [
            '_id',
            'username',
            'avatar',
        ],
        'is_liked',
        'replies_count',
        'first_reply',
        'media_id',
        'user_id',
    ];

    public function testFetchMediaComments()
    {
        $user = User::query()->first();
        $mediaComment = MediaComment::query()->first();
        $media = $mediaComment->mediaObj;

        $r = $this->actingAs($user)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->get("/v1/media-comment/{$media->_id}");

        $r->assertStatus(200);

        $r->assertJsonStructure([
            'data' => [
                'items' => [self::MEDIA_COMMENT_STRUCTURE]
            ]
        ]);
    }

    public function testFetchMediaCommentReplies()
    {
        $user = User::query()->first();
        $mediaComment = MediaComment::query()->whereNotNull('parent')->first();
        $parentId = $mediaComment->parent['_id'];

        $r = $this->actingAs($user)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->get("/v1/media-comment/{$parentId}/replies");

        $r->assertStatus(200);

        $r->assertJsonStructure([
            'data' => [
                'items' => [self::MEDIA_COMMENT_REPLY_STRUCTURE]
            ]
        ]);
    }

    public function testReportComment()
    {
        $user = User::query()->first();
        $mediaComment = MediaComment::query()->first();

        $r = $this->actingAs($user)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->post("/v1/media-comment/{$mediaComment->_id}/report", [
                'reason' => 'This comment contains something weird..',
            ]);

        $r->assertStatus(201);

        $r->assertJsonStructure([
            'data' => [
                '_id',
                'comment_id',
                'reason',
                'created_at',
                'updated_at',
            ],
        ]);

        $data = $r->decodeResponseJson()['data'];

        $this->assertDatabaseHas((new Report())->getCollection(), [
            '_id' => new ObjectId($data['_id']),
            'reason' => $data['reason'],
        ]);
    }

    /**
     * @test
     */
    public function testLikeMediaComment()
    {
        $user = User::query()->first();
        $mediaComment = MediaComment::query()->first();
        $mediaComment->likes_count = 0;
        $mediaComment->save();

        $r = $this->actingAs($user)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->patch("/v1/media-comment/{$mediaComment->_id}/like");

        $r->assertStatus(200)->assertJsonPath('status', 'OK');
        $r->assertJsonPath('code', 200);

        $r->assertJsonStructure([
                'data' => self::MEDIA_COMMENT_STRUCTURE,
            ])->assertJson(
                fn (AssertableJson $json) => $json->whereAllType(self::MEDIA_COMMENT_TYPES)
            );

        $this->assertDatabaseHas((new MediaCommentLike())->getCollection(), [
            'created_by' => new ObjectId($user->_id),
            'media_comment_id' => new ObjectId($mediaComment->_id),
        ]);

        $this->assertDatabaseHas((new MediaComment())->getCollection(), [
            '_id' => $mediaComment->_id,
            'likes_count' => 1,
        ]);

        $r = $this->actingAs($user)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->patch("/v1/media-comment/{$mediaComment->_id}/unlike");

        $r->assertStatus(200)->assertJsonPath('status', 'OK');
        $r->assertJsonPath('code', 200);

        $r->assertJsonStructure([
            'data' => self::MEDIA_COMMENT_STRUCTURE,
        ])->assertJson(
            fn (AssertableJson $json) => $json->whereAllType(self::MEDIA_COMMENT_TYPES)
        );

        $this->assertDatabaseMissing((new MediaCommentLike())->getCollection(), [
            'created_by' => new ObjectId($user->_id),
            'media_comment_id' => new ObjectId($mediaComment->_id),
        ]);

        $this->assertDatabaseHas((new MediaComment())->getCollection(), [
            '_id' => $mediaComment->_id,
            'likes_count' => 0,
        ]);
    }

    public function testCreateMediaComment()
    {
        $user = User::query()->first();
        $media = Media::query()->first();
        $media->save();

        $r = $this->actingAs($user)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->post("/v1/media-comment/{$media->_id}", [
                'text' => $this->faker->realText(),
            ]);

        $r->assertStatus(201)->assertJsonPath('status', 'OK');
        $r->assertJsonPath('code', 201);

        $r->assertJsonStructure([
            'data' => self::MEDIA_COMMENT_STRUCTURE,
        ])->assertJson(
            fn (AssertableJson $json) => $json->whereAllType(self::MEDIA_COMMENT_TYPES)
        );
    }

    public function testCreateMediaCommentReply()
    {
        $user = User::query()->first();
        $mediaComment = MediaComment::query()->first();

        $r = $this->actingAs($user)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->post("/v1/media-comment/{$mediaComment->_id}/reply", [
                'text' => $this->faker->realText(),
            ]);

        $r->assertStatus(201)->assertJsonPath('status', 'OK');
        $r->assertJsonPath('code', 201);

        $types = [
            'code' => 'integer',
            'status' => 'string',
            'data._id' => 'string',
            'data.created_at' => 'integer',
            'data.likes_count' => 'integer',
            'data.user_id' => 'string',
            'data.creator' => 'array',
            'data.creator._id' => 'string',
            'data.creator.username' => 'string',
            'data.creator.avatar' => 'string',
        ];

        $r->assertJsonStructure([
            'data' => self::MEDIA_COMMENT_REPLY_STRUCTURE
        ]);

        $r->assertJson(
            fn (AssertableJson $json) => $json->whereAllType($types)
        );
    }
}
