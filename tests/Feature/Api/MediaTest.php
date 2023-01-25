<?php

namespace Aparlay\Core\Tests\Feature\Api;

use Aparlay\Core\Models\Enums\FollowStatus;
use Aparlay\Core\Models\Enums\MediaStatus;
use Aparlay\Core\Models\Enums\MediaVisibility;
use Aparlay\Core\Models\Enums\UserStatus;
use Aparlay\Core\Models\Enums\UserVisibility;
use Aparlay\Core\Models\Follow;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use MongoDB\BSON\ObjectId;

class MediaTest extends ApiTestCase
{
    /**
     * @test
     */
    public function getMediaId()
    {
        $media = Media::factory()->create([
            'status' => MediaStatus::COMPLETED->value, 'visibility' => MediaVisibility::PUBLIC->value,
        ]);
        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->json('GET', '/v1/media/'.$media->_id, [])
            ->assertStatus(200)
            ->assertJsonPath('status', 'OK')
            ->assertJsonPath('code', 200)
            ->assertJsonStructure([
                'data' => [
                    '_id',
                    'description',
                    'hash',
                    'size',
                    'length',
                    'mime_type',
                    'visibility',
                    'status',
                    'hashtags',
                    'people',
                    'file',
                    'cover',
                    'creator' => [
                        '_id',
                        'username',
                        'avatar',
                    ],
                    'is_liked',
                    'is_visited',
                    'like_count',
                    'likes',
                    'visit_count',
                    'visits',
                    'comment_count',
                    'comments',
                    'is_adult',
                    'slug',
                    'created_by',
                    'updated_by',
                    'created_at',
                    'updated_at',
                    '_links',
                ],
            ])->assertJson(
                fn (AssertableJson $json) => $json->whereAllType([
                    'code' => 'integer',
                    'status' => 'string',
                    'uuid' => 'string',
                    'data._id' => 'string',
                    'data.description' => 'string',
                    'data.hash' => 'string',
                    'data.size' => 'integer',
                    'data.length' => 'integer',
                    'data.mime_type' => 'string',
                    'data.visibility' => 'integer',
                    'data.status' => 'integer',
                    'data.hashtags' => 'array',
                    'data.people' => 'array',
                    'data.file' => 'string',
                    'data.cover' => 'string',
                    'data.creator' => 'array',
                    'data.creator.avatar' => 'string',
                    'data.creator.username' => 'string',
                    'data.creator._id' => 'string',
                    'data.is_liked' => 'boolean',
                    'data.is_visited' => 'boolean',
                    'data.like_count' => 'integer',
                    'data.likes' => 'array',
                    'data.visit_count' => 'integer',
                    'data.visits' => 'array',
                    'data.comment_count' => 'integer',
                    'data.comments' => 'array',
                    'data.is_adult' => 'boolean',
                    'data.slug' => 'string',
                    'data.created_by' => 'string',
                    'data.updated_by' => 'string',
                    'data.created_at' => 'integer',
                    'data.updated_at' => 'integer',
                    'data._links' => 'array',
                ])
            );
    }

    /**
     * @test
     */
    public function createMediaBySplitUpload()
    {
        $activeUser = User::factory()->create(['status' => UserStatus::ACTIVE->value]);
        $nonActiveUser = User::factory()->create(['status' => UserStatus::PENDING->value]);
        $taggedUser = User::factory()->create();

        $videoFilePath = __DIR__.'/assets/video.mp4';
        $this->assertTrue(file_exists($videoFilePath));
        $videoFile = UploadedFile::fake()->createWithContent('video.mp4', file_get_contents($videoFilePath));

        $this->actingAs($nonActiveUser)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->post('/v1/media', [
                'description' => 'This is test description #test #testAPi @'.$taggedUser->username,
                'file' => $videoFile,
            ])
            ->assertStatus(403);

        $response = $this->actingAs($activeUser)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->post('/v1/media/upload/split', [
                'description' => 'This is test description #test #testAPi @'.$taggedUser->username,
                'flowChunkNumber' => 1,
                'flowChunkSize' => 31457280,
                'flowCurrentChunkSize' => $videoFile->getSize(),
                'flowTotalSize' => $videoFile->getSize(),
                'flowIdentifier' => $videoFile->getClientOriginalName().'-'.$videoFile->getSize(),
                'flowFilename' => $videoFile->getClientOriginalName(),
                'flowRelativePath' => $videoFile->getClientOriginalName(),
                'flowTotalChunks' => 1,
                'file' => $videoFile,
            ])
            ->assertStatus(201)
            ->assertJsonPath('status', 'OK')
            ->assertJsonPath('code', 201)
            ->assertJsonStructure([
                'data' => [
                    'file',
                ],
            ])->decodeResponseJson();

        $this->actingAs($nonActiveUser)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->post('/v1/media', [
                'description' => 'This is test description #test #testAPi @'.$taggedUser->username,
                'file' => $videoFile,
            ])
            ->assertStatus(403);

        $this->actingAs($activeUser)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->post('/v1/media', [
                'description' => 'This is test description #test #testAPi @'.$taggedUser->username,
                'file' => $response['data']['file'],
            ])
            ->assertStatus(201)
            ->assertJsonPath('status', 'OK')
            ->assertJsonPath('code', 201)
            ->assertJsonStructure([
                'data' => [
                    '_id',
                    'description',
                    'hash',
                    'size',
                    'length',
                    'mime_type',
                    'visibility',
                    'status',
                    'hashtags' => [],
                    'people' => [],
                    'file',
                    'cover',
                    'creator' => [
                        '_id',
                        'username',
                        'avatar',
                        'is_followed',
                        'avatar',
                    ],
                    'is_liked',
                    'is_visited',
                    'is_adult',
                    'like_count',
                    'likes',
                    'visit_count',
                    'visits',
                    'comment_count',
                    'comments',
                    'slug',
                    'alerts',
                    'created_by',
                    'updated_by',
                    'created_at',
                    'updated_at',
                    '_links' => [
                        'self' => [
                            'href',
                        ],
                        'index' => [
                            'href',
                        ],
                    ],
                ],
            ])->assertJson(
                fn (AssertableJson $json) => $json->whereAllType([
                    'code' => 'integer',
                    'uuid' => 'string',
                    'status' => 'string',
                    'data.alerts' => 'array',
                    'data.comment_count' => 'integer',
                    'data.comments' => 'array',
                    'data.cover' => 'string',
                    'data.created_at' => 'integer',
                    'data.created_by' => 'string',
                    'data.visibility' => 'integer',
                    'data.creator' => 'array',
                    'data.creator.avatar' => 'string',
                    'data.creator.is_followed' => 'boolean',
                    'data.creator.username' => 'string',
                    'data.creator._id' => 'string',
                    'data.description' => 'string',
                    'data.file' => 'string',
                    'data.hash' => 'null|string',
                    'data.hashtags' => 'array',
                    'data.is_adult' => 'boolean',
                    'data.is_liked' => 'boolean',
                    'data.is_visited' => 'boolean',
                    'data.length' => 'null|integer',
                    'data.like_count' => 'integer',
                    'data.likes' => 'array',
                    'data.mime_type' => 'null|string',
                    'data.people' => 'array',
                    'data.size' => 'null|integer',
                    'data.slug' => 'string',
                    'data.status' => 'integer',
                    'data.updated_at' => 'integer',
                    'data.updated_by' => 'string',
                    'data.visit_count' => 'integer',
                    'data.visits' => 'array',
                    'data._links' => 'array',
                    'data._links.self' => 'array',
                    'data._links.self.href' => 'string',
                    'data._links.index' => 'array',
                    'data._links.index.href' => 'string',
                ])
            );

        $this->assertDatabaseHas('medias', ['created_by' => new ObjectId($activeUser->_id)]);
    }

    /**
     * @test
     */
    public function createMediaByStreamUpload()
    {
        $activeUser = User::factory()->create(['status' => UserStatus::ACTIVE->value]);
        $nonActiveUser = User::factory()->create(['status' => UserStatus::PENDING->value]);
        $taggedUser = User::factory()->create();

        $fileData = [
            'file' => UploadedFile::fake()->create(uniqid().'.mp4', 100),
        ];

        $response = $this->actingAs($activeUser)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->post('/v1/media/upload/stream', $fileData)
            ->assertStatus(201)
            ->assertJsonPath('status', 'OK')
            ->assertJsonPath('code', 201)
            ->assertJsonStructure([
                'data' => [
                    'file',
                ],
            ])->decodeResponseJson();

        $this->actingAs($nonActiveUser)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->post('/v1/media', [
                'description' => 'This is test description #test #testAPi @'.$taggedUser->username,
                'file' => $fileData,
            ])
            ->assertStatus(403);

        $r = $this->actingAs($activeUser)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->post('/v1/media', [
                'description' => 'This is test description #test #testAPi @'.$taggedUser->username,
                'file' => $response['data']['file'],
            ]);

        $r->assertStatus(201)
            ->assertJsonPath('status', 'OK')
            ->assertJsonPath('code', 201)
            ->assertJsonStructure([
                'data' => [
                    '_id',
                    'description',
                    'hash',
                    'size',
                    'length',
                    'mime_type',
                    'visibility',
                    'status',
                    'hashtags' => [],
                    'people' => [],
                    'file',
                    'cover',
                    'creator' => [
                        '_id',
                        'username',
                        'avatar',
                        'is_followed',
                        'avatar',
                    ],
                    'is_liked',
                    'is_visited',
                    'is_adult',
                    'like_count',
                    'likes',
                    'visit_count',
                    'visits',
                    'comment_count',
                    'comments',
                    'slug',
                    'alerts',
                    'created_by',
                    'updated_by',
                    'created_at',
                    'updated_at',
                    '_links' => [
                        'self' => [
                            'href',
                        ],
                        'index' => [
                            'href',
                        ],
                    ],
                ],
            ])->assertJson(
                fn (AssertableJson $json) => $json->whereAllType([
                    'code' => 'integer',
                    'status' => 'string',
                    'data.alerts' => 'array',
                    'data.comment_count' => 'integer',
                    'data.comments' => 'array',
                    'data.cover' => 'string',
                    'data.created_at' => 'integer',
                    'data.created_by' => 'string',
                    'data.visibility' => 'integer',
                    'data.creator' => 'array',
                    'data.creator.avatar' => 'string',
                    'data.creator.is_followed' => 'boolean',
                    'data.creator.username' => 'string',
                    'data.creator._id' => 'string',
                    'data.description' => 'string',
                    'data.file' => 'string',
                    'data.hash' => 'null|string',
                    'data.hashtags' => 'array',
                    'data.is_adult' => 'boolean',
                    'data.is_liked' => 'boolean',
                    'data.is_visited' => 'boolean',
                    'data.length' => 'null|integer',
                    'data.like_count' => 'integer',
                    'data.likes' => 'array',
                    'data.mime_type' => 'null|string',
                    'data.people' => 'array',
                    'data.size' => 'null|integer',
                    'data.slug' => 'string',
                    'data.status' => 'integer',
                    'data.updated_at' => 'integer',
                    'data.updated_by' => 'string',
                    'data.visit_count' => 'integer',
                    'data.visits' => 'array',
                    'data._links' => 'array',
                    'data._links.self' => 'array',
                    'data._links.self.href' => 'string',
                    'data._links.index' => 'array',
                    'data._links.index.href' => 'string',
                ])
            );

        $this->assertDatabaseHas('medias', ['created_by' => new ObjectId($activeUser->_id)]);
    }

    /**
     * @test
     */
    public function testUploadStream()
    {
        $fileData = [
            'file' => UploadedFile::fake()->create(uniqid().'.mp4', 100),
        ];

        $user = User::factory()->create();

        $this->actingAs($user)->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->json('POST', 'v1/media/upload/stream', $fileData)
            ->assertStatus(201)
            ->assertJsonPath('status', 'OK')
            ->assertJsonPath('code', 201)
            ->assertJsonStructure([
                'data' => [
                    'file',
                ],
            ])
            ->assertJson(
                fn (AssertableJson $json) => $json->whereAllType([
                    'code' => 'integer',
                    'status' => 'string',
                    'uuid' => 'string',
                    'data.file' => 'string',
                ])
            );
    }

    /**
     * @test
     */
    public function mediaCreatePolicy()
    {
        $user = User::factory()->create(['status' => 0]);
        $videoFile = UploadedFile::fake()->create(Str::random(6).'.mp4', 1024 * 1024 * 300, 'video/mp4');
        $media = Media::factory()->for(User::factory()->create(['status' => 0]), 'userObj')->create();
        $this->actingAs($user)->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->json('POST', '/v1/media/', [
                'description' => 'image',
                'file' => $videoFile,
            ])
            ->assertStatus(403)
            ->assertJsonPath('code', 403)
            ->assertJsonPath('status', 'ERROR')
            ->assertJsonPath('message', 'You need to complete registration first!');
    }

    /**
     * @test
     */
    public function mediaUpdatePolicy()
    {
        $user = User::factory()->create(['status' => 0]);
        $media = Media::factory()->for(User::factory()->create(['status' => 0]), 'userObj')->create([
            'status' => MediaStatus::COMPLETED->value,
        ]);
        $this->actingAs($user)->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->json('PUT', '/v1/media/'.$media->_id, [
                'description' => 'image 12',
            ])
            ->assertStatus(403)
            ->assertJson([
                'code' => 403,
                'status' => 'ERROR',
                'data' => [],
                'message' => 'You can only update media that you\'ve created.',
            ]);
    }

    /**
     * @test
     */
    public function mediaViewPolicy()
    {
        $user = User::factory()->create(['status' => UserStatus::ACTIVE->value]);
        $media = Media::factory()
            ->for(User::factory()->create(['visibility' => UserVisibility::PRIVATE->value]), 'userObj')
            ->create(['status' => MediaStatus::COMPLETED->value, 'visibility' => MediaVisibility::PRIVATE->value]);
        $this->actingAs($user)->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->json('GET', '/v1/media/'.$media->_id, [])
            ->assertStatus(403)
            ->assertJson([
                'code' => 403,
                'status' => 'ERROR',
                'data' => [],
                'message' => 'This content is not available for you.',
            ]);
    }

    /**
     * @test
     */
    public function mediaDeletePolicy()
    {
        $user = User::factory()->create();
        $media = Media::factory()
            ->for(User::factory()->create(['visibility' => UserVisibility::PUBLIC->value]), 'userObj')
            ->create(['status' => MediaStatus::COMPLETED->value, 'visibility' => MediaVisibility::PUBLIC->value]);
        $this->actingAs($user)->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->json('delete', '/v1/media/'.$media->_id, [])
            ->assertStatus(403)
            ->assertJson([
                'code' => 403,
                'status' => 'ERROR',
                'data' => [],
                'message' => 'You can only delete media that you\'ve created.',
            ]);
    }

    /**
     * @test
     */
    public function isProtectedMediaDeletePolicy()
    {
        $mediaCreator = User::factory()->create();
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
        $this->actingAs($mediaCreator)->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->json('delete', '/v1/media/'.$media->_id, [])
            ->assertStatus(403)
            ->assertJson([
                'code' => 403,
                'status' => 'ERROR',
                'data' => [],
                'message' => 'You are not allowed to delete this. Please contact support for more information.',
            ]);
    }

    /**
     * @test
     */
    public function deleteMedia()
    {
        // Shortens a number and attaches K, M, B, etc. accordingly
        function numberShorten($number, $precision = 1)
        {
            // Setup default $divisors if not provided
            $divisors = [
                1 => '',
                pow(1000, 1) => __('k'),
                pow(1000, 2) => __('m'),
                pow(1000, 3) => __('b'),
                pow(1000, 4) => __('t'),
            ];

            foreach ($divisors as $divisor => $shorthand) {
                if (abs($number) < ($divisor * 1000)) {
                    break;
                }
            }

            return number_format($number / $divisor, $precision).$shorthand;
        }

        $user = User::factory()->create(['media_count' => 2]);
        $otherUser = User::factory()->create();
        $media = Media::factory()->create(
            [
                'status' => MediaStatus::COMPLETED->value,
                'created_by' => new ObjectId($user->_id),
                'creator' => [
                    '_id' => new ObjectId($user->_id),
                    'username' => $user->username,
                    'avatar' => $user->avatar,
                ],
            ]
        );
        Media::factory()->create(
            [
                'status' => MediaStatus::COMPLETED->value,
                'created_by' => new ObjectId($user->_id),
                'creator' => [
                    '_id' => new ObjectId($user->_id),
                    'username' => $user->username,
                    'avatar' => $user->avatar,
                ],
            ]
        );

        $this->assertDatabaseHas((new User())->getCollection(), [
            '_id' => $user->_id,
            'stats.counters.medias' => 2,
        ]);
        $this->actingAs($otherUser)->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->json('DELETE', '/v1/media/'.$media->_id)
            ->assertStatus(403);

        $this->actingAs($user)->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->json('DELETE', '/v1/media/'.$media->_id)
            ->assertStatus(204);

        $this->assertDatabaseHas((new Media())->getCollection(), [
            '_id' => new ObjectId($media->_id),
            'status' => MediaStatus::USER_DELETED->value,
            'created_by' => new ObjectId($user->_id),
            'creator._id' => new ObjectId($user->_id),
        ]);
        $this->assertDatabaseHas((new User())->getCollection(), [
            '_id' => $user->_id,
            'stats.counters.medias' => 1,
        ]);
    }

    /**
     * @test
     */
    public function getByUser()
    {
        $user = User::factory()->create(['status' => UserStatus::ACTIVE->value]);
        $userMediaOwner = User::factory()->create(['status' => UserStatus::ACTIVE->value]);

        Media::factory()->count(2)->create(
            [
                'status' => MediaStatus::CONFIRMED->value,
                'visibility' => MediaVisibility::PUBLIC->value,
                'created_by' => new ObjectId($userMediaOwner->_id),
                'creator' => [
                    '_id' => new ObjectId($userMediaOwner->_id),
                    'username' => $userMediaOwner->username,
                    'avatar' => $userMediaOwner->avatar,
                ],
            ],
        );
        Media::factory()->create(
            [
                'status' => MediaStatus::COMPLETED->value,
                'visibility' => MediaVisibility::PRIVATE->value,
                'created_by' => new ObjectId($userMediaOwner->_id),
                'creator' => [
                    '_id' => new ObjectId($userMediaOwner->_id),
                    'username' => $userMediaOwner->username,
                    'avatar' => $userMediaOwner->avatar,
                ],
            ],
        );

        $expectJsonStructure = [
            'data' => [
                'items' => [
                    [
                        '_id',
                        'description',
                        'hash',
                        'size',
                        'length',
                        'mime_type',
                        'visibility',
                        'status',
                        'hashtags',
                        'people',
                        'file',
                        'cover',
                        'creator' => [
                            '_id',
                            'username',
                            'avatar',
                            'is_followed',
                            'avatar',
                        ],
                        'is_liked',
                        'is_visited',
                        'is_adult',
                        'like_count',
                        'likes',
                        'visit_count',
                        'visits',
                        'comment_count',
                        'comments',
                        'slug',
                        'alerts',
                        'created_by',
                        'updated_by',
                        'created_at',
                        'updated_at',
                        '_links' => [
                            'self' => [
                                'href',
                            ],
                            'index' => [
                                'href',
                            ],
                        ],
                    ],
                ],
                '_links' => [
                    'first' => [
                        'href',
                    ],
                    'last' => [
                        'href',
                    ],
                ],
                '_meta' => [
                    'total_count',
                    'page_count',
                    'current_page',
                    'per_page',
                ],
            ],
        ];

        $assertableJson = [
            'code' => 'integer',
            'uuid' => 'string',
            'status' => 'string',
            'data.items' => 'array',
            'data.items.0._id' => 'string',
            'data.items.0.description' => 'string',
            'data.items.0.alerts' => 'array',
            'data.items.0.comment_count' => 'integer',
            'data.items.0.comments' => 'array',
            'data.items.0.cover' => 'string',
            'data.items.0.created_at' => 'integer',
            'data.items.0.created_by' => 'string',
            'data.items.0.visibility' => 'integer',
            'data.items.0.creator' => 'array',
            'data.items.0.creator.avatar' => 'string',
            'data.items.0.creator.username' => 'string',
            'data.items.0.creator._id' => 'string',
            'data.items.0.file' => 'string',
            'data.items.0.hash' => 'string',
            'data.items.0.hashtags' => 'array',
            'data.items.0.is_adult' => 'boolean',
            'data.items.0.is_liked' => 'boolean',
            'data.items.0.is_visited' => 'boolean',
            'data.items.0.length' => 'integer',
            'data.items.0.like_count' => 'integer',
            'data.items.0.likes' => 'array',
            'data.items.0.mime_type' => 'string',
            'data.items.0.people' => 'array',
            'data.items.0.size' => 'integer',
            'data.items.0.slug' => 'string',
            'data.items.0.status' => 'integer',
            'data.items.0.updated_at' => 'integer',
            'data.items.0.updated_by' => 'string',
            'data.items.0.visit_count' => 'integer',
            'data.items.0.visits' => 'array',
            'data.items.0._links' => 'array',
            'data.items.0._links.self' => 'array',
            'data.items.0._links.self.href' => 'string',
            'data.items.0._links.index' => 'array',
            'data.items.0._links.index.href' => 'string',
        ];

        $r = $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->get('/v1/user/'.$userMediaOwner->_id.'/media');

        $r->assertStatus(200)
            ->assertJsonPath('status', 'OK')
            ->assertJsonPath('code', 200)
            ->assertJsonStructure($expectJsonStructure)
            ->assertJson(
                fn (AssertableJson $json) => $json->whereAllType($assertableJson)
            )
            ->assertJsonFragment(['total_count' => 2]);

        $r = $this->actingAs($user)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->get('/v1/user/'.$userMediaOwner->_id.'/media');

        $r->assertStatus(200)
            ->assertJsonPath('status', 'OK')
            ->assertJsonPath('code', 200)
            ->assertJsonStructure($expectJsonStructure)
            ->assertJson(
                fn (AssertableJson $json) => $json->whereAllType($assertableJson)
            )
            ->assertJsonFragment(['total_count' => 2]);

        $r = $this->actingAs($userMediaOwner)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->get('/v1/user/'.$userMediaOwner->_id.'/media');

        $r->assertStatus(200)
            ->assertJsonPath('status', 'OK')
            ->assertJsonPath('code', 200)
            ->assertJsonStructure($expectJsonStructure)
            ->assertJson(
                fn (AssertableJson $json) => $json->whereAllType($assertableJson)
            )
            ->assertJsonFragment(['total_count' => 3]);
    }

    /**
     * @test
     */
    public function getMedias()
    {
        $user = User::factory()->create();
        $followerUser = User::factory()->create();
        Media::factory()->count(3)->create(
            [
                'visibility' => MediaVisibility::PUBLIC->value,
                'status' => MediaStatus::CONFIRMED->value,
                'created_by' => new ObjectId($user->_id),
                'creator' => [
                    '_id' => new ObjectId($user->_id),
                    'username' => $user->username,
                    'avatar' => $user->avatar,
                ],
            ]
        );
        Follow::factory()->create(
            [
                'user' => [
                    '_id' => new ObjectId($user->_id),
                    'username' => $user->username,
                    'avatar' => $user->avatar,
                ],
                'creator' => [
                    '_id' => new ObjectId($followerUser->_id),
                    'username' => $followerUser->username,
                    'avatar' => $followerUser->avatar,
                ],
                'status' => FollowStatus::ACCEPTED->value,
            ]
        );

        $expectedJsonStructure = [
            'data' => [
                'items' => [
                    [
                        '_id',
                        'description',
                        'hash',
                        'size',
                        'length',
                        'mime_type',
                        'visibility',
                        'status',
                        'hashtags',
                        'people',
                        'file',
                        'cover',
                        'creator' => [
                            '_id',
                            'username',
                            'avatar',
                            'is_followed',
                        ],
                        'is_liked',
                        'is_visited',
                        'is_adult',
                        'like_count',
                        'likes',
                        'visit_count',
                        'visits',
                        'comment_count',
                        'comments',
                        'slug',
                        'alerts',
                        'created_by',
                        'updated_by',
                        'created_at',
                        'updated_at',
                        '_links' => [
                            'self' => [
                                'href',
                            ],
                            'index' => [
                                'href',
                            ],
                        ],
                    ],
                ],
                '_links' => [
                    'prev' => [
                        'href',
                    ],
                    'first' => [
                        'href',
                    ],
                    'last' => [
                        'href',
                    ],
                    'self' => [
                        'href',
                    ],
                    'next' => [
                        'href',
                    ],
                ],
                '_meta' => [
                    'total_count',
                    'page_count',
                    'current_page',
                    'per_page',
                ],
            ],
        ];

        $expectedJsonStructureFollower = [
            'data' => [
                'items' => [
                    [
                        '_id',
                        'description',
                        'hash',
                        'size',
                        'length',
                        'mime_type',
                        'visibility',
                        'status',
                        'hashtags',
                        'people',
                        'file',
                        'cover',
                        'creator' => [
                            '_id',
                            'username',
                            'avatar',
                            'is_followed',
                        ],
                        'is_liked',
                        'is_visited',
                        'is_adult',
                        'like_count',
                        'likes',
                        'visit_count',
                        'visits',
                        'comment_count',
                        'comments',
                        'slug',
                        'alerts',
                        'created_by',
                        'updated_by',
                        'created_at',
                        'updated_at',
                        '_links' => [
                            'self' => [
                                'href',
                            ],
                            'index' => [
                                'href',
                            ],
                        ],
                    ],
                ],
                '_links' => [
                    'first' => [
                        'href',
                    ],
                    'last' => [
                        'href',
                    ],
                    'self' => [
                        'href',
                    ],
                ],
                '_meta' => [
                    'total_count',
                    'page_count',
                    'current_page',
                    'per_page',
                ],
            ],
        ];

        $assertableJson = [
            'code' => 'integer',
            'status' => 'string',
            'uuid' => 'string',
            'data.items' => 'array',
            'data.items.0.alerts' => 'array',
            'data.items.0.comment_count' => 'integer',
            'data.items.0.comments' => 'array',
            'data.items.0.cover' => 'string',
            'data.items.0.created_at' => 'integer',
            'data.items.0.created_by' => 'string',
            'data.items.0.creator' => 'array',
            'data.items.0.creator.avatar' => 'string',
            'data.items.0.creator.username' => 'string',
            'data.items.0.creator._id' => 'string',
            'data.items.0.description' => 'string',
            'data.items.0.file' => 'string',
            'data.items.0.hash' => 'string',
            'data.items.0.hashtags' => 'array',
            'data.items.0.is_adult' => 'boolean',
            'data.items.0.is_liked' => 'boolean',
            'data.items.0.is_visited' => 'boolean',
            'data.items.0.length' => 'integer',
            'data.items.0.like_count' => 'integer',
            'data.items.0.likes' => 'array',
            'data.items.0.mime_type' => 'string',
            'data.items.0.people' => 'array',
            'data.items.0.size' => 'integer',
            'data.items.0.slug' => 'string',
            'data.items.0.status' => 'integer',
            'data.items.0.updated_at' => 'integer',
            'data.items.0.updated_by' => 'string',
            'data.items.0.visibility' => 'integer',
            'data.items.0.visit_count' => 'integer',
            'data.items.0.visits' => 'array',
            'data.items.0._links' => 'array',
            'data.items.0._links.self' => 'array',
            'data.items.0._links.self.href' => 'string',
            'data.items.0._links.index' => 'array',
            'data.items.0._links.index.href' => 'string',
            'data._links' => 'array',
            'data._links.prev' => 'array',
            'data._links.prev.href' => 'string',
            'data._links.first' => 'array',
            'data._links.first.href' => 'string',
            'data._links.last' => 'array',
            'data._links.last.href' => 'string',
            'data._links.self' => 'array',
            'data._links.self.href' => 'string',
            'data._links.next' => 'array',
            'data._links.next.href' => 'string',
            'data._meta' => 'array',
            'data._meta.per_page' => 'integer',
            'data._meta.current_page' => 'integer',
            'data._meta.page_count' => 'integer',
            'data._meta.total_count' => 'integer',
        ];

        $assertableJsonFollower = [
            'code' => 'integer',
            'status' => 'string',
            'uuid' => 'string',
            'data.items' => 'array',
            'data.items.0.alerts' => 'array',
            'data.items.0.comment_count' => 'integer',
            'data.items.0.comments' => 'array',
            'data.items.0.cover' => 'string',
            'data.items.0.created_at' => 'integer',
            'data.items.0.created_by' => 'string',
            'data.items.0.creator' => 'array',
            'data.items.0.creator.avatar' => 'string',
            'data.items.0.creator.username' => 'string',
            'data.items.0.creator._id' => 'string',
            'data.items.0.description' => 'string',
            'data.items.0.file' => 'string',
            'data.items.0.hash' => 'string',
            'data.items.0.hashtags' => 'array',
            'data.items.0.is_adult' => 'boolean',
            'data.items.0.is_liked' => 'boolean',
            'data.items.0.is_visited' => 'boolean',
            'data.items.0.length' => 'integer',
            'data.items.0.like_count' => 'integer',
            'data.items.0.likes' => 'array',
            'data.items.0.mime_type' => 'string',
            'data.items.0.people' => 'array',
            'data.items.0.size' => 'integer',
            'data.items.0.slug' => 'string',
            'data.items.0.status' => 'integer',
            'data.items.0.updated_at' => 'integer',
            'data.items.0.updated_by' => 'string',
            'data.items.0.visibility' => 'integer',
            'data.items.0.visit_count' => 'integer',
            'data.items.0.visits' => 'array',
            'data.items.0._links' => 'array',
            'data.items.0._links.self' => 'array',
            'data.items.0._links.self.href' => 'string',
            'data.items.0._links.index' => 'array',
            'data.items.0._links.index.href' => 'string',
            'data._links' => 'array',
            'data._links.first' => 'array',
            'data._links.first.href' => 'string',
            'data._links.last' => 'array',
            'data._links.last.href' => 'string',
            'data._links.self' => 'array',
            'data._links.self.href' => 'string',
            'data._meta' => 'array',
            'data._meta.per_page' => 'integer',
            'data._meta.current_page' => 'integer',
            'data._meta.page_count' => 'integer',
            'data._meta.total_count' => 'integer',
        ];

        $this->withHeaders(['X-DEVICE-ID' => uniqid('random-string')])
            ->get('/v1/media')
            ->assertStatus(200)
            ->assertJsonPath('status', 'OK')
            ->assertJsonPath('code', 200)
            ->assertJson(
                fn (AssertableJson $json) => $json->whereAllType($assertableJson)
            )
            ->assertJsonStructure($expectedJsonStructure);

        $this->actingAs($followerUser)
            ->withHeaders(['X-DEVICE-ID' => uniqid('random-string')])
            ->get('/v1/media?type=following')
            ->assertStatus(200)
            ->assertJsonPath('status', 'OK')
            ->assertJsonPath('code', 200)
            ->assertJson(
                fn (AssertableJson $json) => $json->whereAllType($assertableJsonFollower)
            )
            ->assertJsonStructure($expectedJsonStructureFollower);

        $this->actingAs($followerUser)
            ->withHeaders(['X-DEVICE-ID' => uniqid('random-string')])
            ->get('/v1/media')
            ->assertStatus(200)
            ->assertJsonPath('status', 'OK')
            ->assertJsonPath('code', 200)
            ->assertJson(
                fn (AssertableJson $json) => $json->whereAllType($assertableJson)
            )
            ->assertJsonStructure($expectedJsonStructure);
    }

    /**
     * @test
     */
    public function isProtectedMediaViewPolicy()
    {
        $mediaCreator = User::factory()->create();
        $media = Media::factory()->for($mediaCreator, 'userObj')->create([
            'visibility' => MediaVisibility::PRIVATE->value,
            'created_by' => $mediaCreator->_id,
            'status' => MediaStatus::COMPLETED->value,
            'creator' => [
                '_id' => $mediaCreator->_id,
                'username' => $mediaCreator->username,
                'avatar' => $mediaCreator->avatar,
            ],
        ]);
        $mediaViewer = User::factory()->create();
        $this->actingAs($mediaViewer)->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->json('GET', '/v1/media/'.$media->_id, [])
            ->assertStatus(403)
            ->assertJson([
                'code' => 403,
                'status' => 'ERROR',
                'data' => [],
                'message' => 'This content is not available for you.',
            ]);
    }
}
