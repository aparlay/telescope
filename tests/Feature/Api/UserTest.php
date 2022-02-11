<?php

namespace Aparlay\Core\Tests\Feature\Api;

use Aparlay\Core\Api\V1\Controllers\UserController;
use Aparlay\Core\Models\Enums\UserStatus;
use Aparlay\Core\Models\User;

class UserTest extends ApiTestCase
{
    /**
     * @see UserController::destroy()
     */
    public function testDelete()
    {
        $user = User::factory()->create();

        $r = $this->actingAs($user)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->json('POST', '/v1/me/delete', ['reason' => 'just to test me.']);

        $r->assertStatus(204);

        $user->refresh();
        $this->assertSame($user->status, UserStatus::DEACTIVATED->value);
    }
}
