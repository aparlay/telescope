<?php

use Aparlay\Core\Models\Enums\UserStatus;
use App\Models\Media;
use App\Models\User;
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
            foreach (Media::query()->creator((string) $user->_id)->lazy() as $media) {
                $media->unsearchable();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
