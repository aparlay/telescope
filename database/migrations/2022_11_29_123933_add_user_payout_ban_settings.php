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
        User::query()->update([
            'setting.payout.ban_payout' => false,
            'setting.payout.auto_ban_payout' => false,
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
};
