<?php

use Illuminate\Database\Migrations\Migration;

class UpdateUserCountry extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Aparlay\Core\Models\User::where('country_alpha2', '!=', null)->each(function ($user) {
            $user->country_alpha2 = null;
            $user->save();
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
