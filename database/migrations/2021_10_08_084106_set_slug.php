<?php

use Aparlay\Core\Models\Alert;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetSlug extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Aparlay\Core\Models\Media::where('slug', null)->chunk(200, function ($models) {
            foreach ($models as $media) {
                $media->slug = \Aparlay\Core\Api\V1\Services\MediaService::generateSlug(6);
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
        //
    }
}
