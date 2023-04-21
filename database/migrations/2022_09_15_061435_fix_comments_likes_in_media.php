<?php

use Illuminate\Database\Migrations\Migration;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (\Aparlay\Core\Models\MediaLike::lazy() as $mediaLike) {
            /** @var \Aparlay\Core\Models\MediaLike $mediaLike */
            $media              = $mediaLike->mediaObj;
            $mediaLike->user_id = new \MongoDB\BSON\ObjectId($media->creator['_id']);
            $mediaLike->save();
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
