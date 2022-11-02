<?php

use Aparlay\Core\Models\Enums\UserGender;
use Aparlay\Core\Models\Enums\UserInterestedIn;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\User;
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
        User::query()->where('interested_in', '=', 0)->update(['interested_in' => [0]]);
        User::query()->where('interested_in', '=', 1)->update(['interested_in' => [1]]);
        User::query()->where('interested_in', '=', 2)->update(['interested_in' => [2]]);
        Media::query()->whereNotNull('is_fake')->update(['content_gender' => [UserInterestedIn::FEMALE->value]]);
        foreach (Media::query()->with('userObj')->whereNull('is_fake')->lazy() as $media) {
            /** @var Media $media */
            $media->content_gender = match ($media->userObj->gender) {
                UserGender::FEMALE->value, UserGender::NOT_MENTION->value => UserInterestedIn::FEMALE->value,
                UserGender::MALE->value => UserInterestedIn::MALE->value,
                UserGender::TRANSGENDER->value => UserInterestedIn::TRANSGENDER->value,
            };
            $media->recalculateSortScores();
            $media->drop('sort_score');
        }
        Schema::table((new Media())->getCollection(), function (Blueprint $table) {
            $table->dropIndex(['status', 'sort_scores.default', 'visibility']);
            $table->dropIndex(['status', 'sort_scores.guest', 'visibility']);
            $table->dropIndex(['status', 'sort_scores.returned', 'visibility']);
            $table->dropIndex(['status', 'sort_scores.registered', 'visibility']);
            $table->dropIndex(['status', 'sort_scores.paid', 'visibility']);
            $table->index(['status', 'sort_scores.default', 'content_gender', 'visibility'], null, ['background' => true]);
            $table->index(['status', 'sort_scores.guest', 'content_gender', 'visibility'], null, ['background' => true]);
            $table->index(['status', 'sort_scores.returned', 'content_gender', 'visibility'], null, ['background' => true]);
            $table->index(['status', 'sort_scores.registered', 'content_gender', 'visibility'], null, ['background' => true]);
            $table->index(['status', 'sort_scores.paid', 'content_gender', 'visibility'], null, ['background' => true]);
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
};
