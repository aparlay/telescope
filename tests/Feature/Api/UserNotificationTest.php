<?php

namespace Aparlay\Core\Tests\Feature\Api;

use Aparlay\Core\Models\Enums\UserNotificationCategory;
use Aparlay\Core\Models\Enums\UserNotificationStatus;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\User;
use Aparlay\Core\Models\UserNotification;
use Illuminate\Testing\Fluent\AssertableJson;
use MongoDB\BSON\ObjectId;

class UserNotificationTest extends ApiTestCase
{
    /**
     * @test
     */
    public function index()
    {
        $user = User::factory()->create();
        $media = Media::factory()->create();
        $userNotification = UserNotification::factory()->create([
            'category' => UserNotificationCategory::LIKES->value,
            'entity._type' => Media::shortClassName(),
            'entity._id' => new ObjectId($media->_id),
            'user_id' => new ObjectId($user->_id),
        ]);

        $response = $this->actingAs($userNotification->userObj)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->json('GET', '/v1/user-notification', []);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'OK')
            ->assertJsonPath('code', 200)
            ->assertJsonStructure([
                'data' => [
                    'items' => [
                        [
                            '_id',
                            'status',
                            'status_label',
                            'category',
                            'category_label',
                            'message',
                            'payload',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                    '_links' => [],
                    '_meta' => [
                        'per_page',
                    ],
                ],
            ])->assertJson(
                fn (AssertableJson $json) => $json->whereAllType([
                    'code' => 'integer',
                    'uuid' => 'string',
                    'status' => 'string',
                    'data.items.0._id' => 'string',
                    'data.items.0.status' => 'integer',
                    'data.items.0.status_label' => 'string',
                    'data.items.0.category' => 'integer',
                    'data.items.0.category_label' => 'string',
                    'data.items.0.message' => 'string',
                    'data.items.0.payload' => 'array|null',
                    'data.items.0.created_at' => 'integer',
                    'data.items.0.updated_at' => 'integer',
                ])
            );
    }
}
