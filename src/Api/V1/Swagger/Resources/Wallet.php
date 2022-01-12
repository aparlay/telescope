<?php

/**
 * @OA\Schema()
 */
class Wallet
{
    /**
     * @OA\Property(property="_id", type="string", example="60237caf5e41025e1e3c80b1")
     * @OA\Property(property="status", type="integer", description="pending=1, verified=2, rejected=-1", example="-1")
     * @OA\Property(property="status_label", type="string", description="created, confirmed, rejected", example="created")
     *
     * @OA\Property(property="type", type="integer", description="paypal=1, bank=2, cryptocurrency=3", example="1")
     * @OA\Property(property="type_label", type="string", description="bank, cryptocurrency, paypal", example="paypal")
     */
}
