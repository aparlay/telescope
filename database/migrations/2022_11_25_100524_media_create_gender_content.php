<?php

use Aparlay\Core\Models\Enums\MediaContentGender;
use Aparlay\Core\Models\Enums\UserSettingShowAdultContent;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\User;
use Illuminate\Database\Migrations\Migration;
use Jenssegers\Mongodb\Schema\Blueprint;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table((new Media())->getCollection(), function (Blueprint $table) {
            $table->dropIndexIfExists(['status', 'sort_scores.default', 'visibility']);
            $table->dropIndexIfExists(['status', 'sort_scores.guest', 'visibility']);
            $table->dropIndexIfExists(['status', 'sort_scores.returned', 'visibility']);
            $table->dropIndexIfExists(['status', 'sort_scores.registered', 'visibility']);
            $table->dropIndexIfExists(['status', 'sort_scores.paid', 'visibility']);
        });
        Media::query()->update(['content_gender' => MediaContentGender::FEMALE->value]);

        User::query()->where('setting.show_adult_content', true)
            ->update(['setting.show_adult_content' => UserSettingShowAdultContent::ALL->value]);
        User::query()->where('setting.show_adult_content', false)
            ->update(['setting.show_adult_content' => UserSettingShowAdultContent::ASK->value]);
        User::query()->where('setting.show_adult_content', null)
            ->update(['setting.show_adult_content' => UserSettingShowAdultContent::ASK->value]);
        User::query()->where('setting.filter_content_gender', null)
            ->update(['setting.filter_content_gender' => [
                MediaContentGender::FEMALE->label() => true,
                MediaContentGender::MALE->label() => true,
                MediaContentGender::TRANSGENDER->label() => true,
            ]]);

        Schema::table((new Media())->getCollection(), function (Blueprint $table) {
            $table->index(['status', 'sort_scores.default', 'visibility', 'content_gender'], null, ['background' => true]);
            $table->index(['status', 'sort_scores.guest', 'visibility', 'content_gender'], null, ['background' => true]);
            $table->index(['status', 'sort_scores.returned', 'visibility', 'content_gender'], null, ['background' => true]);
            $table->index(['status', 'sort_scores.registered', 'visibility', 'content_gender'], null, ['background' => true]);
            $table->index(['status', 'sort_scores.paid', 'visibility', 'content_gender'], null, ['background' => true]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table((new Media())->getCollection(), function (Blueprint $table) {
            $table->dropIndexIfExists(['status', 'sort_scores.default', 'visibility', 'content_gender']);
            $table->dropIndexIfExists(['status', 'sort_scores.guest', 'visibility', 'content_gender']);
            $table->dropIndexIfExists(['status', 'sort_scores.returned', 'visibility', 'content_gender']);
            $table->dropIndexIfExists(['status', 'sort_scores.registered', 'visibility', 'content_gender']);
            $table->dropIndexIfExists(['status', 'sort_scores.paid', 'visibility', 'content_gender']);
        });

        Schema::table((new Media())->getCollection(), function (Blueprint $table) {
            $table->index(['status', 'sort_scores.default', 'visibility'], null, ['background' => true]);
            $table->index(['status', 'sort_scores.guest', 'visibility'], null, ['background' => true]);
            $table->index(['status', 'sort_scores.returned', 'visibility'], null, ['background' => true]);
            $table->index(['status', 'sort_scores.registered', 'visibility'], null, ['background' => true]);
            $table->index(['status', 'sort_scores.paid', 'visibility'], null, ['background' => true]);
        });
    }
};
