<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (\Aparlay\Core\Models\MediaLike::lazy() as $mediaLike) {
            /** @var \Aparlay\Core\Models\MediaLike $mediaLike */
            $media = $mediaLike->mediaObj;
            $mediaLike->user_id = new \MongoDB\BSON\ObjectId($media->creator['_id']);
            $mediaLike->save();
        }
        foreach (\Aparlay\Core\Models\MediaVisit::lazy() as $mediaVisit) {
            /** @var \Aparlay\Core\Models\MediaVisit $mediaVisit */
            $media = $mediaVisit->mediaObj;
            $mediaVisit->user_id = new \MongoDB\BSON\ObjectId($media->creator['_id']);
            $mediaVisit->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        return;
    }
};
