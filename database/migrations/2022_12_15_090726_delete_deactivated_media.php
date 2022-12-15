<?php

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Models\Enums\MediaStatus;
use Aparlay\Core\Models\Enums\UserStatus;
use Aparlay\Core\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (User::where('status', UserStatus::DEACTIVATED->value)->lazy() as $user) {
            Media::creator($user->_id)->update(['status' => MediaStatus::USER_DELETED->value]);
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
