<?php

use Aparlay\Core\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use MongoDB\BSON\ObjectId;

class DefineSettingFromFiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $settings = [
            'core' => [
                'admin' => [
                    'lists' => [
                        'page_count' => 20,
                        'user_page_count' => 5,
                    ],
                ],
            ],
            'app' => [
                'name' => env('APP_NAME', 'Waptap'),
                'timezone' => 'UTC',
                'locale' => 'en',
                'adminEmail' => 'admin@waptap.com',
                'supportEmail' => 'support@waptap.com',
                'senderEmail' => 'noreply@waptap.com',
                'senderName' => 'Waptap.com mailer',
                'user' => [
                    'passwordResetTokenExpire' => 3600,
                    'passwordMinLength' => 8,
                    'defaultSetting' => [
                        'otp' => false,
                        'notifications' => [
                            'unread_message_alerts' => false,
                            'new_followers' => false,
                            'news_and_updates' => false,
                            'tips' => false,
                            'new_subscribers' => false,
                        ],
                    ],
                ],
                'otp' => [
                    'enabled' => false,
                    'length' => [
                        'min' => 1000,
                        'max' => 9999,
                    ],
                    'duration' => 600,
                    'invalid_attempt_limit' => 5,
                ],
                'sms' => [
                    'numbers' => ['+110008284', '+120000110220'],
                    'messages' => [
                        'otp' => "Your OTP code to use in Waptap application:\n\n%token%",
                    ],
                ],
                'email' => [
                    'noreply' => 'noreply@waptap.com',
                    'src_alt_name' => 'Waptap',
                    'logo' => 'https://assets.waptap.com/web/waptap.png',
                    'templates' => [
                        'email_verification' => 'email_verification',
                    ],
                ],
            ],
        ];

        foreach ($settings as $key => $setting) {
            foreach ($setting as $name => $value) {
                $array = [
                    'group' => $key,
                    'created_by' => new ObjectId(),
                    'updated_by' => new ObjectId(),
                    'title' => $name,
                    'value' => $value,
                ];

                Setting::create($array);
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
        Setting::truncate();
    }
}
