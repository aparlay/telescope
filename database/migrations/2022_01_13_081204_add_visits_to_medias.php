<?php

use Illuminate\Database\Migrations\Migration;

class AddVisitsToMedias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Aparlay\Core\Models\Media::where('visits', null)->chunk(200, function ($models) {
            foreach ($models as $media) {
                $media->visits = [];
                $media->save();
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
    }
}
