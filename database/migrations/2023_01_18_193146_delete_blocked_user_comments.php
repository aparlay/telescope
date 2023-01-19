<?php

use Aparlay\Core\Jobs\DeleteUserMediaComments;
use Aparlay\Core\Models\Enums\UserStatus;
use Aparlay\Core\Models\MediaComment;
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
        foreach (User::where('status', UserStatus::BLOCKED->value)->lazy() as $user) {
            if (MediaComment::query()->creator((string) $user->_id)->first()) {
                DeleteUserMediaComments::dispatch((string) $user->_id);
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
