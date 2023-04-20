<?php

namespace Aparlay\Core\Tests\Feature\Api;

use Aparlay\Core\Api\V1\Models\Version;
use Illuminate\Testing\Fluent\AssertableJson;

class VersionTest extends ApiTestCase
{
    /**
     * @test
     */
    public function version()
    {
        $version = Version::factory()->create();
        $this->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->json('GET', '/v1/version/' . $version->os . '/' . $version->version, [])
            ->assertStatus(200)
            ->assertJsonPath('status', 'OK')
            ->assertJsonPath('code', 200)
            ->assertJsonStructure([
                'data' => [
                    'require_force_update',
                    'version' => [
                        '_id',
                        'os',
                        'app',
                        'version',
                        'is_force_update',
                        'expired_at',
                        'created_by',
                        'updated_by',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ])->assertJson(
                fn (AssertableJson $json) => $json->whereAllType([
                    'code' => 'integer',
                    'status' => 'string',
                    'uuid' => 'string',
                    'data.require_force_update' => 'boolean',
                    'data.version' => 'array',
                    'data.version._id' => 'string',
                    'data.version.os' => 'string',
                    'data.version.app' => 'string',
                    'data.version.version' => 'integer',
                    'data.version.is_force_update' => 'boolean',
                    'data.version.expired_at' => 'array',
                    'data.version.created_by' => 'string',
                    'data.version.updated_by' => 'string',
                    'data.version.created_at' => 'string',
                    'data.version.updated_at' => 'string',
                ])
            );
    }
}
