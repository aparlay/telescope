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
            foreach (Media::creator($user->_id)->lazy() as $media) {
                $media->status = MediaStatus::USER_DELETED->value;
                $media->save();
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
