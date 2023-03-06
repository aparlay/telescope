<?php

namespace Aparlay\Core\Tests\Feature\Api;

use Aparlay\Core\Api\V1\Controllers\UserDocumentController;
use Aparlay\Core\Api\V1\Models\UserDocument;
use Aparlay\Core\Models\Enums\UserDocumentType;
use Aparlay\Core\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use MongoDB\BSON\ObjectId;

class UserDocumentTest extends ApiTestCase
{
    /**
     * @see UserDocumentController::index()
     * @return void
     */
    public function testIndex()
    {
        $userDocument = UserDocument::first();

        $r = $this->actingAs($userDocument->creatorObj)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->get('/v1/user-document');

        $r->assertStatus(200);

        $r->assertJsonStructure([
            'data' => [
                ['_id', 'type', 'status', 'url', 'status_label', 'type_label'],
            ],
        ]);

        $r->assertJson(
            fn ($json) => $json->whereAllType([
                'code' => 'integer',
                'uuid' => 'string',
                'status' => 'string',
                'data' => 'array',
            ])
        );
    }

    /**
     * @see UserDocumentController::view()
     * @return void
     */
    public function testView()
    {
        /** @var UserDocument $userDocument */
        $userDocument = UserDocument::first();

        $r = $this->actingAs($userDocument->creatorObj)
            ->withHeaders(['X-DEVICE-ID' => 'random-string'])
            ->get("/v1/user-document/$userDocument->_id");

        $r->assertStatus(200);
        $r->assertJsonStructure([
            'data' => [
                '_id', 'type', 'status', 'url',
            ],
        ]);

        $r->assertJson(
            fn ($json) => $json->whereAllType([
                'code' => 'integer',
                'uuid' => 'string',
                'status' => 'string',
                'data.type' => 'integer',
                'data.status' => 'integer',
                'data.status_label' => 'string',
                'data.type_label' => 'string',
                'data.url' => 'string',
            ])
        );
    }

    /**
     * @see UserDocumentController::store()
     */
    public function testCreate()
    {
        Bus::fake();
        Event::fake();
        Storage::fake('upload');

        $user = User::factory()->create();

        $idCardFile = UploadedFile::fake()->create('id_card.jpg', 100);
        $selfie = UploadedFile::fake()->create('selfie.jpg', 100);

        $documentTypes = [
            UserDocumentType::ID_CARD->value => $idCardFile,
            UserDocumentType::SELFIE->value => $selfie,
        ];

        foreach ($documentTypes as $documentType => $uploadedFile) {
            $r = $this->actingAs($user)
                ->withHeaders(['X-DEVICE-ID' => 'random-string'])
                ->post('/v1/user-document', [
                    'file' => $uploadedFile,
                    'type' => $documentType,
                ]);

            $r->assertJsonStructure([
                'data' => [
                    '_id', 'type', 'status', 'status_label', 'type_label',
                ],
            ]);

            $r->assertJson(
                fn ($json) => $json->whereAllType([
                    'code' => 'integer',
                    'uuid' => 'string',
                    'status' => 'string',
                    'data.type' => 'integer',
                    'data.status' => 'integer',
                    'data.status_label' => 'string',
                    'data.type_label' => 'string',
                ])
            );

            $dR = $r->decodeResponseJson();
            $rDocumentType = Arr::get($dR, 'data.type');
            $this->assertSame($documentType, $rDocumentType);

            $modelId = Arr::get($dR, 'data._id');
            $this->assertDatabaseHas('user_documents', ['_id' => new ObjectId($modelId), 'creator._id' => new ObjectId($user->_id)]);
        }
    }
}
