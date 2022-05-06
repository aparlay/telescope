<?php

use Illuminate\Database\Migrations\Migration;
use Aparlay\Core\Database\Seeders\CountrySeeder;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Artisan::call('db:seed', [
            '--class' => CountrySeeder::class,
            '--force'   => true
        ]);
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
