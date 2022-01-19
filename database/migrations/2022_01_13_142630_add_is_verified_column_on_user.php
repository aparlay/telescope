<?php

use Aparlay\Core\Models\User;
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
        User::update(['is_verified' => false]);
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
