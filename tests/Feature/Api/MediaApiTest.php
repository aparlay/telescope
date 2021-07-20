<?php

namespace Aparlay\Core\Tests\Feature\Commands;

use Aparlay\Core\Tests\Feature\Api\ApiTestCase;

class MediaApiTest extends ApiTestCase
{
    /** @test */
    public function get_medias_id()
    {
        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])->json('GET', '/v1/media', [])
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                    'api_token',
                ],
            ]);
    }
}
