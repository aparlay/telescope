<?php

use Aparlay\Core\Models\MediaComment;
use Aparlay\Core\Models\MediaLike;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (MediaComment::lazy() as $mediaComment) {
            /** @var MediaComment $mediaComment */
            $media      = $mediaComment->mediaObj;
            $creatorObj = $mediaComment->creatorObj;
            if (empty($media) || empty($creatorObj)) {
                $mediaComment->deleteQuietly();

                continue;
            }

            $media->updateComments();
        }
        foreach (MediaLike::lazy() as $mediaLike) {
            /** @var MediaLike $mediaLike */
            $media      = $mediaLike->mediaObj;
            $creatorObj = $mediaLike->creatorObj;
            if (empty($media) || empty($creatorObj)) {
                $mediaLike->deleteQuietly();

                continue;
            }
            $media->updateLikes();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('media', function (Blueprint $table) {
        });
    }
};
