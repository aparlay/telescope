<?php

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\MediaComment;
use Aparlay\Core\Models\MediaLike;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MongoDB\BSON\ObjectId;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (MediaComment::lazy() as $mediaComment) {
            $media = $mediaComment->mediaObj;
            $commentCount = MediaComment::query()->media($media->_id)->count();
            $media->comment_count = $commentCount;
            $media->addToSet('comments', [
                '_id' => new ObjectId($mediaComment->creator['_id']),
                'username' => $mediaComment->creator['username'],
                'avatar' => $mediaComment->creator['avatar'],
            ], 10);
            $media->count_fields_updated_at = array_merge(
                $media->count_fields_updated_at,
                ['comments' => DT::utcNow()]
            );
            $media->save();
        }
        foreach (MediaLike::lazy() as $mediaLike) {
            $media = $mediaLike->mediaObj;
            $commentCount = MediaComment::query()->media($media->_id)->count();
            $media->like_count = $commentCount;
            $media->addToSet('likes', [
                '_id' => new ObjectId($mediaLike->creator['_id']),
                'username' => $mediaLike->creator['username'],
                'avatar' => $mediaLike->creator['avatar'],
            ], 10);
            $media->count_fields_updated_at = array_merge(
                $media->count_fields_updated_at,
                ['likes' => DT::utcNow()]
            );
            $media->save();
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
            //
        });
    }
};
