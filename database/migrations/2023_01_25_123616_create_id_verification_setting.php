<?php

use Aparlay\Core\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MongoDB\BSON\ObjectId;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Setting::create([
            'group' => 'core',
            'created_by' => new ObjectId(),
            'updated_by' => new ObjectId(),
            'title' => 'id_verification_thresholds',
            'value' => [
                'min_likes' => 1000,
                'min_followers' => 100,
                'min_medias' => 1,
            ],
        ]);
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
