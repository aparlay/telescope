<?php

namespace Aparlay\Core\Tests\Feature\Api;

use Aparlay\Core\Api\V1\Models\Version;
use Aparlay\Core\Models\Enums\UserNotificationCategory;
use Aparlay\Core\Models\User;
use Aparlay\Core\Models\UserNotification;
use Illuminate\Testing\Fluent\AssertableJson;

class UserNotificationTest extends ApiTestCase
{
    /**
     * @test
     */
    public function index()
    {
        $user = User::factory()->create();
        $userNotification = UserNotification::factory()->create([
            'user_id' => $user->_id,
            'category' => UserNotificationCategory::SYSTEM->value,
        ]);
        $r = $this->actingAs($userNotification->userObj)->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->json('GET', '/v1/user-notification', []);

        $r->dump();
        $r->assertStatus(200)
            ->assertJsonPath('status', 'OK')
            ->assertJsonPath('code', 200)
            ->assertJsonStructure([
                'data' => [
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
            ])->assertJson(
                fn (AssertableJson $json) => $json->whereAllType([
                    'code' => 'integer',
                    'status' => 'string',
                    'data._id' => 'string',
                    'data.status' => 'integer',
                    'data.status_label' => 'string',
                    'data.category' => 'integer',
                    'data.category_label' => 'string',
                    'data.message' => 'string',
                    'data.entity' => 'null',
                    'data.created_at' => 'integer',
                    'data.updated_at' => 'integer',
                ])
            );
    }
}
