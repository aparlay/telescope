<?php

use Aparlay\Core\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        User::whereNotNull('user_agents')->chunk(200, function ($models) {
            foreach ($models as $user) {
                $userAgents        = [];
                foreach ($user->user_agents as $key => $agent) {
                    $userAgents[] = [
                        'key' => $key,
                        'device_id' => $agent['device_id'],
                        'user_agent' => $agent['user_agent'],
                        'ip' => $agent['ip'],
                        'created_at' => $agent['created_at'],
                    ];
                }
                $user->user_agents = $userAgents;
                $user->save();
            }
        });

        Schema::table((new User())->getCollection(), function (Blueprint $table) {
            $table->index(['user_agents.ip'], null, ['background' => true]);
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
};
