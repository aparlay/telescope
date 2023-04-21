<?php

use Aparlay\Core\Models\Enums\UserStatus;
use Aparlay\Core\Models\Media;
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
        foreach (User::where('status', UserStatus::SUSPENDED->value)->lazy() as $user) {
            $user->searchable();
            Media::query()->creator((string) $user->_id)->searchable();
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
