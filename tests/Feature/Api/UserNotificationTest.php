<?php

namespace Aparlay\Core\Tests\Feature\Api;

use Aparlay\Core\Models\Enums\UserNotificationCategory;
use Aparlay\Core\Models\Enums\UserNotificationStatus;
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
        $userNotification = UserNotification::latest()->first();

        $r = $this->actingAs($userNotification->userObj)->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->json('GET', '/v1/user-notification', []);

        $r->assertStatus(200)
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
                            'entity',
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
                    'status' => 'string',
                    'data.items.0._id' => 'string',
                    'data.items.0.status' => 'integer',
                    'data.items.0.status_label' => 'string',
                    'data.items.0.category' => 'integer',
                    'data.items.0.category_label' => 'string',
                    'data.items.0.message' => 'string',
                    'data.items.0.entity' => 'array',
                    'data.items.0.created_at' => 'integer',
                    'data.items.0.updated_at' => 'integer',
                ])
            );
    }
}
