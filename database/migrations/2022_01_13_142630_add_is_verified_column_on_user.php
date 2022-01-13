<?php

use Illuminate\Database\Migrations\Migration;

class AddIsVerifiedColumnOnUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Aparlay\Core\Models\User::chunk(200, function ($models) {
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
