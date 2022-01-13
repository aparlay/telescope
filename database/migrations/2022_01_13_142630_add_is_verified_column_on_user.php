<?php

use Illuminate\Database\Migrations\Migration;
use Aparlay\Core\Models\User;

class AddIsVerifiedColumnOnUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        User::chunk(200, function ($models) {
            foreach ($models as $user) {
                $user->is_verified = false;
                $user->save();
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
        //
    }
}
