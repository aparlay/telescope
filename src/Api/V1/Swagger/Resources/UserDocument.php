<?php

/**
 * @OA\Schema()
 */
class UserDocument
{
    /**
     * @OA\Property(property="_id", type="string", example="60237caf5e41025e1e3c80b1")
     * @OA\Property(property="type", type="integer", description="id_card=0, selfie=1")
     * @OA\Property(property="status", type="integer", description="created=0, confirmed=1")
     */
}
