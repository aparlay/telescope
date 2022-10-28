<?php

use Aparlay\Core\Helpers\DT;
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

        \Aparlay\Core\Models\User::whereNotNull('user_agents')->chunk(200, function ($models) {
            foreach ($models as $user) {
                $userAgents = [];
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
