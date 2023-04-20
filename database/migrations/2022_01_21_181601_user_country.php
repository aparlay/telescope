<?php

use Illuminate\Database\Migrations\Migration;

class UserCountry extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $alpha2 = [];

        foreach (\Aparlay\Core\Models\Country::query()->get() as $country) {
            $alpha2[] = $country->alpha2;
        }

        \Aparlay\Core\Models\User::where('country_alpha2', null)->each(function ($user) use ($alpha2) {
            $user->country_alpha2 = Arr::random($alpha2);
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
