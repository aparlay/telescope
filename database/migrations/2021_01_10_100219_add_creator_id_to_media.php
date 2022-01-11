<?php

use Aparlay\Core\Models\Media;
use Illuminate\Database\Migrations\Migration;
use MongoDB\BSON\ObjectId;

class AddCreatorIdToMedia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Media::get()->transform(function ($item) {
            $media = $item->getRawOriginal();
            $creator = $media['creator'];

            if (! isset($creator['_id'])) {
                $creator['_id'] = new ObjectId($item->created_by);
                $media['creator'] = $creator;
                Media::where('_id', $item->_id)->update(['creator' => $creator]);
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
