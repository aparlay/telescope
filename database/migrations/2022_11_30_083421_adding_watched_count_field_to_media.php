<?php

use Aparlay\Core\Models\Media;
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
        foreach (Media::query()->whereNull('is_fake')->get() as $media) {
            /** @var Media $media */
            $media->watched_count = $media->visit_count;
            $media->saveQuietly();
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
