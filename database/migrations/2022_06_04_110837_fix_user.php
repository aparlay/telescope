<?php

use Aparlay\Core\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (User::lazy() as $user) {
            $setting       = $user->setting;
            if (!isset($user->setting['payment'])) {
                $setting['payment'] = [
                    'allow_unverified_cc' => false,
                    'block_unverified_cc' => false,
                    'block_cc_payments' => false,
                    'unverified_cc_spent_amount' => 0,
                ];
            } else {
                $setting['payment']['unverified_cc_spent_amount'] = $setting['payment']['spent_amount'] ?? 0;
                unset($setting['payment']['spent_amount']);
            }
            $user->setting = $setting;
            $user->saveQuietly();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
};
