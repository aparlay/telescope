<?php

namespace Aparlay\Core\Tests\Feature\Api;

use Aparlay\Core\Api\V1\Models\Block;
use Aparlay\Core\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use MongoDB\BSON\ObjectId;

class UserTest extends ApiTestCase
{
    /**
     * @test
     */
    public function testDelete()
    {
        $model = User::factory()->create();
        $user = User::factory()->create();

        $r = $this->actingAs($user)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->json('DELETE', '/v1/me', []);

        $r->assertStatus(204);
    }
}
