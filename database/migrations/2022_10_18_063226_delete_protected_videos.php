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
        foreach (User::where('verification_status', UserVerificationStatus::VERIFIED->value)->whereNull('is_fake')->lazy() as $user) {
            Media::where('creator._id', new ObjectId($user->_id))
                ->orWhere('is_protected', '!=', true)
                ->orWhere('is_music_licensed', '!=', true)
                ->orWhere('status', '!=', MediaStatus::CONFIRMED->value)
                ->update([
                    'status' => MediaStatus::CONFIRMED->value,
                    'is_protected' => true,
                    'is_music_licensed' => true,
                ]);
        }
        Media::where('is_protected', null)->update([
            'status' => MediaStatus::CONFIRMED->value,
            'is_protected' => false,
            'is_music_licensed' => false,
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
