<?php

use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\User;
use Illuminate\Database\Migrations\Migration;
use MongoDB\BSON\ObjectId;

class AddCreatorIdToMedia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Media::get()->transform(function ($item) {
            $media   = $item->getRawOriginal();
            $creator = $media['creator'];

            if (!isset($creator['_id']) || User::user($creator['_id'])->first() === null) {
                $user                = User::limit(200)->get()->random()->first();
                $creator['_id']      = new ObjectId($user->_id);
                $creator['username'] = $user->username;
                $creator['avatar']   = $user->avatar;
                $media['creator']    = $creator;
                Media::where('_id', $item->_id)->update(['creator' => $creator]);
            }
        });
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
