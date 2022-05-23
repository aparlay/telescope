<?php

use Aparlay\Core\Models\User;
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
        foreach (User::lazy() as $user) {
            if (empty($user->setting)) {
                $user->update(['setting' => [
                    'otp' => false,
                    'notifications' => [
                        'unread_message_alerts' => false,
                        'new_followers' => false,
                        'news_and_updates' => false,
                        'tips' => false,
                        'new_subscribers' => false,
                    ],
                    'risk' => [
                        'block_unverified_cc' => false,
                        'spent_amount' => 0,
                    ],
                ]]);
            }
            if (empty($user->setting['risk'])) {
                $setting = $user->setting;
                $setting['risk'] = [
                    'block_unverified_cc' => false,
                    'spent_amount' => 0,
                ];
                $user->update(['setting' => $setting]);
            }
            if (empty($user->setting['otp'])) {
                $setting = $user->setting;
                $setting['otp'] = false;
                $user->update(['setting' => $setting]);
            }
            if (empty($user->setting['notifications'])) {
                $setting = $user->setting;
                $setting['notifications'] = [
                    'unread_message_alerts' => false,
                    'new_followers' => false,
                    'news_and_updates' => false,
                    'tips' => false,
                    'new_subscribers' => false,
                ];
                $user->update(['setting' => $setting]);
            }
        }
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
