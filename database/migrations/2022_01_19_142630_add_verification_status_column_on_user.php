<?php

use Aparlay\Core\Models\Enums\UserVerificationStatus;
use Aparlay\Core\Models\User;
use Illuminate\Database\Migrations\Migration;

class AddVerificationStatusColumnOnUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        User::where('verification_status', null)->update(['verification_status' => UserVerificationStatus::UNVERIFIED->value]);
    }

    /**
     * Reverse the migrations.
     *
     * @return voida
     */
    public function down()
    {

    }
}
