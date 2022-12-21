<?php

use Aparlay\Core\Models\Enums\MediaStatus;
use Aparlay\Core\Models\Enums\UserVerificationStatus;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\User;
use Illuminate\Database\Migrations\Migration;
use MongoDB\BSON\ObjectId;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (User::where('verification_status', UserVerificationStatus::VERIFIED->value)->whereNull('is_fake')->get() as $user) {
            foreach (Media::where('creator._id', new ObjectId($user->_id))->whereNull('is_fake')->get() as $media) {
                if ($media->is_protected !== true || $media->is_music_licensed !== true || $media->status !== MediaStatus::CONFIRMED->value) {
                    $media->status = MediaStatus::CONFIRMED->value;
                    $media->is_protected = true;
                    $media->is_music_licensed = true;
                    $media->save();
                }
            }
        }
        foreach (Media::where('is_protected', null)->whereNull('is_fake')->get() as $media) {
            $media->is_protected = false;
            $media->is_music_licensed = false;
            $media->save();
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
