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
        $default = [
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
            'block_unverified_cc' => false,
        ];
        foreach (User::lazy() as $user) {
            $setting = $user->setting;
            if (empty($setting)) {
                $setting = $default;
            }
            if (empty($user->setting['risk'])) {
                $setting['risk'] = [
                    'block_unverified_cc' => false,
                    'spent_amount' => 0,
                ];
            }
            if (empty($user->setting['block_unverified_cc'])) {
                $setting['block_unverified_cc'] = false;
            }
            if (empty($user->setting['otp'])) {
                $setting['otp'] = false;
            }
            if (empty($user->setting['notifications'])) {
                $setting['notifications'] = [
                    'unread_message_alerts' => false,
                    'new_followers' => false,
                    'news_and_updates' => false,
                    'tips' => false,
                    'new_subscribers' => false,
                ];
            }

            $user->update(['setting' => $setting]);
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
