<?php

use Aparlay\Core\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        User::query()->chunk(100, function ($users) {
            foreach ($users as $user) {
                $text_search = explode(' ', $user->full_name);
                $text_search[] = $user->username;
                $text_search[] = $user->email;
                $text_search[] = $user->phone_number;
                $user->text_search = array_values(array_filter(array_map('strtolower', $text_search)));
                $user->saveQuietly();
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
};
