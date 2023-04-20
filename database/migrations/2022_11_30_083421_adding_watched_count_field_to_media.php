<?php

use Aparlay\Core\Models\Media;
use Illuminate\Database\Migrations\Migration;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (Media::query()->where('is_fake', ['$exists' => false])->get() as $media) {
            /** @var Media $media */
            $media->watched_count = $media->visit_count;
            $media->visit_count   = 3 * $media->visit_count;
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
