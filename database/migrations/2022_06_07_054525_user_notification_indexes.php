<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table((new \Aparlay\Core\Models\UserNotification())->getCollection(), function (Blueprint $table) {
            $table->index(['user_id', 'updated_at', 'category'], null, ['background' => true]);
            $table->index(['user_id', 'entity._id', 'entity._type', 'category'], null, ['background' => true]);
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
