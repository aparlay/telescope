<?php

namespace Aparlay\Core\Tests\Feature\Api;

use Aparlay\Core\Models\Enums\MediaStatus;
use Aparlay\Core\Models\Enums\MediaVisibility;
use Aparlay\Core\Models\Enums\UserStatus;
use Aparlay\Core\Models\Enums\UserType;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\MediaComment;
use Aparlay\Core\Models\MediaCommentLike;
use Aparlay\Core\Models\Report;
use Aparlay\Core\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Illuminate\Testing\Fluent\AssertableJson;
use MongoDB\BSON\ObjectId;

class MediaCommentTest extends ApiTestCase
{
    public const MEDIA_COMMENT_TYPES           = [
        'code' => 'integer',
        'status' => 'string',
        'uuid' => 'string',
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
    public const MEDIA_COMMENT_REPLY_STRUCTURE = [
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
        ];
    public const MEDIA_COMMENT_STRUCTURE       = [
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

    public function setUp(): void
    {
        parent::setUp();
        Queue::fake();
    }

    public function test_fetch_media_comments()
    {
        $user         = $this->createTestUser();
        $mediaCreator = $this->createTestUser();
        $media        = $this->createTestMedia($mediaCreator);
        $this->createTestComment($user, $media);

        $r            = $this->actingAs($user)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->get("/v1/media/{$media->_id}/comment");

        $r->assertStatus(200);

        $r->assertJsonStructure([
            'data' => [
                'items' => [self::MEDIA_COMMENT_STRUCTURE],
            ],
        ]);
    }

    public function test_fetch_media_comment_replies()
    {
        $user                        = $this->createTestUser();
        $replyToUser                 = $this->createTestUser();

        $mediaCreator                = $this->createTestUser();
        $media                       = $this->createTestMedia($mediaCreator);
        $mediaCommentParent          = $this->createTestComment($user, $media);
        $mediaComment                = $this->createTestComment($user, $media);
        $mediaComment->parent        = [
            '_id' => new ObjectId($mediaCommentParent->_id),
        ];
        $mediaComment->reply_to_user = [
            '_id' => new ObjectId($replyToUser->_id),
            'username' => $replyToUser->username,
            'avatar' => $replyToUser->avatar,
        ];
        $mediaComment->saveQuietly();

        $r                           = $this->actingAs($user)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->get("/v1/media/{$mediaComment->media_id}/comment/{$mediaCommentParent->_id}/reply");

        $r->assertStatus(200);

        $r->assertJsonStructure([
            'data' => [
                'items' => [self::MEDIA_COMMENT_REPLY_STRUCTURE],
            ],
        ]);
    }

    public function test_report_comment()
    {
        $user         = $this->createTestUser();
        $mediaCreator = $this->createTestUser();
        $media        = $this->createTestMedia($mediaCreator);
        $mediaComment = $this->createTestComment($user, $media);

        $r            = $this->actingAs($user)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->post("/v1/media/{$mediaComment->media_id}/comment/{$mediaComment->_id}/report", [
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

        $data         = $r->decodeResponseJson()['data'];

        $this->assertDatabaseHas((new Report())->getCollection(), [
            '_id' => new ObjectId($data['_id']),
            'reason' => $data['reason'],
        ]);
    }

    /**
     * @test
     */
    public function test_like_media_comment()
    {
        $user         = $this->createTestUser();
        $mediaCreator = $this->createTestUser();
        $media        = $this->createTestMedia($mediaCreator);
        $mediaComment = $this->createTestComment($user, $media);

        $r            = $this->actingAs($user)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->patch("/v1/media/{$mediaComment->media_id}/comment/{$mediaComment->_id}/like");

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

        $r            = $this->actingAs($user)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->delete("/v1/media/{$mediaComment->media_id}/comment/{$mediaComment->_id}/like");

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

    public function test_create_media_comment()
    {
        $user         = $this->createTestUser();
        $mediaCreator = $this->createTestUser();
        $media        = $this->createTestMedia($mediaCreator);

        $r            = $this->actingAs($user)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->post("/v1/media/{$media->_id}/comment", [
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

    public function test_create_media_comment_reply_failed()
    {
        $user                       = $this->createTestUser();
        $mediaCreator               = User::factory()->create(['like_count' => 0]);
        $media                      = $this->createTestMedia($mediaCreator);
        $media->is_comments_enabled = false;
        $media->saveQuietly();

        $r                          = $this->actingAs($user)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->post("/v1/media/{$media->_id}/comment", [
                'text' => $this->faker->realText(),
            ]);

        $r->assertStatus(403);
    }

    public function test_create_media_comment_reply()
    {
        $mediaOwner   = $this->createTestUser();
        $media        = $this->createTestMedia($mediaOwner);
        $user         = $this->createTestUser();
        $mediaComment = $this->createTestComment($user, $media);

        $r            = $this->actingAs($user)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->post("/v1/media/{$mediaComment->media_id}/comment/{$mediaComment->_id}/reply", [
                'text' => $this->faker->realText(),
            ]);

        $r->assertStatus(201)->assertJsonPath('status', 'OK');
        $r->assertJsonPath('code', 201);

        $types        = [
            'code' => 'integer',
            'status' => 'string',
            'uuid' => 'string',
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
            'data' => self::MEDIA_COMMENT_REPLY_STRUCTURE,
        ]);

        $r->assertJson(
            fn (AssertableJson $json) => $json->whereAllType($types)
        );
    }

    private function createTestUser()
    {
        return User::factory()->create([
            'type' => UserType::USER->value,
            'password_hash' => Hash::make('waptap'),
            'status' => UserStatus::ACTIVE->value,
            'email' => uniqid() . '@waptap.com',
        ]);
    }

    private function createTestMedia($user)
    {
        return Media::factory()->for($user, 'userObj')->create([
            'is_comments_enabled' => true,
            'is_protected' => false,
            'created_by' => $user->_id,
            'like_count' => 0,
            'status' => MediaStatus::COMPLETED->value,
            'visibility' => MediaVisibility::PUBLIC->value,
            'creator' => [
                '_id' => $user->_id,
                'username' => $user->username,
                'avatar' => $user->avatar,
            ],
        ]);
    }

    private function createTestComment($user, $media)
    {
        return MediaComment::factory()->create([
            'media_id' => new ObjectId($media->_id),
            'user_id' => new ObjectId($user->_id),
            'likes_count' => 0,
        ]);
    }
}
