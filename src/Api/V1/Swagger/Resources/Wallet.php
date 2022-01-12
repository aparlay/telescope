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
    *    @OA\Property(
    *       property="data",
    *       type="array",
    *       example={{
    *         "email": "waptap.user@paypal.com",
    *       }, {
    *          "account_holder_name": "Alia Abernathy",
    *          "routing_number": "TXCPWMV2",
    *          "account_number": "VMGCOUQDT7R"
    *       },
     *      {
     *          "wallet_address": "1BvBMSEYstWetqTFn5Au4m4GFg7xJaNVN2",
     *          "currency": "BTC",
     *       },
     *     },
    *       @OA\Items(
    *             @OA\Property(
    *                property="email",
    *                type="string",
    *                example="waptap.user@paypal.com"
    *             ),
    *             @OA\Property(
    *                property="account_number",
    *                type="string",
    *                example="1234826701935923512"
    *             ),
    *             @OA\Property(
    *                property="account_holder_name",
    *                type="string",
    *                example="Alia Abernathy"
    *             ),
    *             @OA\Property(
    *                property="routing_number",
    *                type="string",
    *                example="586586586"
    *             ),
    *             @OA\Property(
    *                property="wallet_address",
    *                type="string",
    *                example="1BvBMSEYstWetqTFn5Au4m4GFg7xJaNVN2"
    *             ),
    *             @OA\Property(
    *                property="currency",
    *                type="string",
    *                example="BTC, ETH"
    *             ),
    *       ),
    *    ),
    */
}
