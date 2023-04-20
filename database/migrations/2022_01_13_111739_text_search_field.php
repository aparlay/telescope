<?php

use Illuminate\Database\Migrations\Migration;

class TextSearchField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Aparlay\Core\Models\User::where('text_search', null)->each(function ($user) {
            $text_search       = explode(' ', $user->full_name);
            $text_search[]     = $user->username;
            $text_search[]     = $user->email;
            $text_search[]     = $user->phone_number;
            $user->text_search = $text_search;
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
