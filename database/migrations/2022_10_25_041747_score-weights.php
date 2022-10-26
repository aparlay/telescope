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
        $settings = [
            'app' => [
                'media' => [
                    'visit_multiplier' => 7,
                    'awesomeness_score_weight' => 0.3,
                    'beauty_score_weight' => 0.3,
                    'like_score_weight' => 0.1,
                    'visit_score_weight' => 0.1,
                    'time_score_weight' => 0.2,
                ],
            ],
        ];

        foreach ($settings as $key => $setting) {
            foreach ($setting as $name => $value) {
                $array = [
                    'group' => $key,
                    'created_by' => new ObjectId(),
                    'updated_by' => new ObjectId(),
                    'title' => $name,
                    'value' => $value,
                ];

                Setting::create($array);
            }
        }
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
