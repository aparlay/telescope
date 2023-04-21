<?php

use Illuminate\Database\Migrations\Migration;

class AddUserFeatures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Aparlay\Core\Models\User::where('features.wallet_cryptocurrency', null)->update([
            'features' => [
                'tips' => false,
                'exclusive_content' => false,
                'wallet_bank' => false,
                'wallet_paypal' => false,
                'wallet_cryptocurrency' => false,
                'demo' => false,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
