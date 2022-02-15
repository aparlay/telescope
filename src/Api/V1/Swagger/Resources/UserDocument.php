<?php

namespace Aparlay\Core\Api\V1\Swagger\Resources;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema()
 */
class UserDocument
{
    /**
     * @OA\Property(property="_id", type="string", example="60237caf5e41025e1e3c80b1")
     */
    public $_id;

    /**
     * @OA\Property(property="type", type="integer", description="id_card=0, selfie=1", example="1")
     */
    public $type;

    /**
     * @OA\Property(property="type_label", type="string", description="id_card, selfie", example="id card")
     */
    public $type_label;

    /**
     * @OA\Property(property="status", type="integer", description="created=0, confirmed=1, rejected=-1", example="0")
     */
    public $status;

    /**
     * @OA\Property(property="status_label", type="string", description="created, confirmed, rejected", example="created")
     */
    public $status_label;

    /**
     * @OA\Property(property="url", type="string", description="url to b2 storage", example="https://waptap-dev-documents.s3.us-west-000.backblazeb2.com/user_document_61c9959a9c5bdc285d71a14661cb0d173bba2.png?X-Amz-Content-Sha256=UNSIGNED-PAYLOAD&X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=0000a6daeaf64b4000000000f%2F20211228%2Fus-west-000%2Fs3%2Faws4_request&X-Amz-Date=20211228T164735Z&X-Amz-SignedHeaders=host&X-Amz-Expires=600&X-Amz-Signature=83927170f7aac189413a52a77b67c78e864bfbba385dd040e990aeb26d8cec02")
     */
    public $url;

    /**
     * @OA\Property(property="alerts", type="array", @OA\Items (ref="#/components/schemas/AlertUserDocument"))
     */
    public $alerts;
}
