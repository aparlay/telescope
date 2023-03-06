<?php

use Aparlay\Core\Models\MediaComment;
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
        Schema::table((new MediaComment())->getCollection(), function (Blueprint $table) {
            $table->index(['media_id', 'created_at', 'user_id'], null, ['background' => true]);
        });

        foreach (MediaComment::query()->lazy() as $mediaComment) {
            $mediaComment->update(['user_id' => new ObjectId($mediaComment->mediaObj->creator['_id'] ?? $mediaComment->mediaObj->created_by)]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table((new MediaComment())->getCollection(), function (Blueprint $table) {
            $table->dropIndex(['media_id', 'created_at', 'user_id']);
        });

        foreach (MediaComment::query()->lazy() as $mediaComment) {
            $mediaComment->update(['user_id' => new ObjectId($mediaComment->creator['_id'])]);
        }
    }
};
