<?php

namespace Aparlay\Core\Tests\Feature\Api;

use Illuminate\Testing\Fluent\AssertableJson;

class SiteTest extends ApiTestCase
{
    /**
     * @test
     */
    public function cache()
    {
        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->json('GET', '/v1/cache/', [])
            ->assertStatus(200)
            ->assertJsonPath('status', 'OK')
            ->assertJsonPath('code', 200)
            ->assertJsonStructure([
                'data' => [
                    'current',
                    'max',
                    'percent',
                    'ttl',
                ],
            ])->assertJson(
                fn (AssertableJson $json) => $json->whereAllType([
                    'code' => 'integer',
                    'uuid' => 'string',
                    'status' => 'string',
                    'data.current' => 'integer',
                    'data.max' => 'integer',
                    'data.percent' => 'double',
                    'data.ttl' => 'string',
                ])
            );
    }
}
