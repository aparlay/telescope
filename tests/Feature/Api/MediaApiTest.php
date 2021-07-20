<?php


namespace Aparlay\Core\Tests\Feature\Commands;


use Aparlay\Core\Tests\Feature\Api\ApiTestCase;

class MediaApiTest extends ApiTestCase
{
    /** @test */
    public function get_medias_id()
    {
        $response = $this->withHeaders([
            'X-DEVICE-ID' => 'random-string',
        ])->get('/media');
        $this->json('POST', 'media', $payload)
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
