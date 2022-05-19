<?php

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
        User::query()->update(['scores' => ['sort' => 0, 'risk' => 0]]);
        foreach (User::query()->whereNotNull('sort_score')->get() as $user) {
            $user->update(['scores' => ['sort' => $user->sort_score, 'risk' => 0]]);
            $user->drop('sort_score');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
};
