<?php

use Aparlay\Core\Models\User;
use Illuminate\Database\Migrations\Migration;

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
            'payment' => [
                'allow_unverified_cc' => false,
                'block_unverified_cc' => false,
                'block_cc_payments' => false,
                'unverified_cc_spent_amount' => 0,
            ],
            'block_unverified_cc' => false,
        ];
        foreach (User::lazy() as $user) {
            $setting = $user->setting;

            if (empty($setting)) {
                $setting = $default;
            }

            unset($setting['block_unverified_cc'], $setting['risk']);

            if (empty($user->setting['payment'])) {
                $setting['payment'] = [
                    'allow_unverified_cc' => false,
                    'block_unverified_cc' => false,
                    'block_cc_payments' => false,
                    'unverified_cc_spent_amount' => 0,
                ];
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

            $user->setting = $setting;
            $user->saveQuietly();
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
