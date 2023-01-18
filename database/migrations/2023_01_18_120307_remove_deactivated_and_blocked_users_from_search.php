<?php

use Aparlay\Core\Models\Enums\UserStatus;
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
        foreach (User::whereIn('status', [UserStatus::DEACTIVATED->value, UserStatus::BLOCKED->value])->lazy() as $user) {
            $user->unsearchable();
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
