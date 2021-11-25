<?php

use Aparlay\Core\Models\Setting;
use Aparlay\Core\Models\User;
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
                    'domain' => env('ADMIN_DOMAIN', 'ltoptop.waptap.dev'),
                    'lists' => [
                        'page_count' => 20,
                        'user_page_count' => 5,
                    ],
                ],
            ],
            'app' => [
                'name' => env('APP_NAME', 'Laravel'),
                'env' => env('APP_ENV', 'production'),
                'debug' => (bool) env('APP_DEBUG', false),
                'url' => env('APP_URL', 'http://localhost'),
                'asset_url' => env('ASSET_URL', null),
                'timezone' => 'UTC',
                'locale' => 'en',
                'is_testing' => false,
                'adminEmail' => 'admin@waptap.com',
                'supportEmail' => 'support@waptap.com',
                'senderEmail' => 'noreply@waptap.com',
                'senderName' => 'Waptap.com mailer',
                'cache' => [
                    'veryLongDuration' => 432000,
                    'longDuration' => 86400,
                    'mediumDuration' => 3600,
                    'shortDuration' => 180,
                ],
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
                'domain' => 'waptap.test',
                'main_domain' => env('MAIN_DOMAIN', 'www.waptap.test'),
                'frontend_url' => env('WEB_APP_URL', 'https://app.waptap.test'),
                'admin_url' => env('ADMIN_URL', 'https://admin.waptap.test'),
                'admin_urls' => [
                    'profile' => env('ADMIN_URL', 'https://admin.waptap.test').'/user/view?id=',
                    'media' => env('ADMIN_URL', 'https://admin.waptap.test').'/media/view?id=',
                ],
                'web_app_urls' => [
                    'profile' => 'https://'.env('APP_DOMAIN', 'https://app.waptap.test').'/profile/',
                    'share' => 'https://'.env('APP_DOMAIN', 'https://app.waptap.test').'/s/',
                    'home' => 'https://'.env('APP_DOMAIN', 'https://app.waptap.test'),
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
                'dbQueryCacheDuration' => 3600,
                'uploadUrl' => 'https://upload.waptap.dev',
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
            'auth' => [
                'defaults' => [
                    'guard' => 'api',
                    'passwords' => 'users',
                ],
                'guards' => [
                    'admin' => [
                        'driver' => 'session',
                        'provider' => 'users',
                    ],
                    'web' => [
                        'driver' => 'session',
                        'provider' => 'users',
                    ],
                    'api' => [
                        'driver' => 'jwt',
                        'provider' => 'users',
                    ],
                ],
            ],

        ];
        $user = User::where('type', User::TYPE_ADMIN)->first();
        foreach ($settings as $key => $setting) {
            foreach ($setting as $name => $value) {
                $array = [
                    'group' => $key,
                    'created_by' => new ObjectId($user->_id),
                    'updated_by' => new ObjectId($user->_id),
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
