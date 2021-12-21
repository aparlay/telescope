<?php

use App\Models\Media;
use Illuminate\Database\Migrations\Migration;

class AddDefaultValueForTipInMedia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Media::where('tips', null)->chunk(200, function ($models) {
            foreach ($models as $media) {
                $media->tips = 0;
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
