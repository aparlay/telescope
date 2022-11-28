<?php

use Aparlay\Core\Models\Enums\NoteCategory;
use Aparlay\Core\Models\Enums\NoteType;
use Aparlay\Core\Models\Note;
use Illuminate\Database\Migrations\Migration;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Note::each(function($model) {
            $model->category = (NoteType::OTHER->value === $model->type ? NoteCategory::NOTE->value : NoteCategory::LOG->value);
            $model->save();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        return true;
    }
};
