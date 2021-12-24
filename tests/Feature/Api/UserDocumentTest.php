<?php

namespace Aparlay\Core\Tests\Feature\Api;

use Aparlay\Core\Api\V1\Controllers\UserDocumentController;
use Aparlay\Core\Models\Enums\UserDocumentType;
use Aparlay\Core\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

class UserDocumentTest extends ApiTestCase
{
    /**
     * @see UserDocumentController::store()
     */
    public function testCreate()
    {
        $user = User::factory()->create();

        $uploadedFile = UploadedFile::fake()->create('fakefile.jpg', 100);

        $documentTypes = [UserDocumentType::ID_CARD->value, UserDocumentType::SELFIE->value];

        foreach ($documentTypes as $documentType) {
            $r = $this->actingAs($user)
                ->withHeaders(['X-DEVICE-ID' => 'random-string'])
                ->post('/v1/user/document', [
                    'file' => $uploadedFile,
                    'type' => $documentType,
                ]);

            $r->assertStatus(201);
            $r->assertJsonStructure([
                'data' => [
                    '_id', 'type', 'status',
                ],
            ]);

            $r->assertJson(
                fn ($json) => $json->whereAllType([
                    'code' => 'integer',
                    'status' => 'string',
                    'data.type' => 'integer',
                    'data.status' => 'integer',
                ])
            );

            $dR = $r->decodeResponseJson();
            $rDocumentType = Arr::get($dR, 'data.type');
            $this->assertSame($documentType, $rDocumentType);
        }
    }
}
