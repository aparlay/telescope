<?php

use Aparlay\Chat\Models\Enums\ChatCategory;
use Aparlay\Chat\Models\Enums\ChatStatus;
use Aparlay\Chat\Models\Message;
use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MongoDB\BSON\ObjectId;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $aluaSupport = User::username('aluasupport')->first();

        if (empty($aluaSupport)) {
            \Aparlay\Core\Models\User::create([
                'type' => 1,
                'status' => 2,
                'username' => 'aluasupport',
                'password_hash' => '$2y$13$5rJM7d0o57g8IZeucGl.m.oCfK6MgiOcZfwYw73X2EW1W8x30lSNy',
                'setting' => [
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
                        'spent_amount' => 0,
                    ],
                ],
                'count_fields_updated_at' => [
                    'followers' => DT::utcNow(),
                    'followings' => DT::utcNow(),
                    'blocks' => DT::utcNow(),
                    'likes' => DT::utcNow(),
                    'medias' => DT::utcNow(),
                    'followed_hashtags' => DT::utcNow(),
                ],
                'created_at' => DT::utcNow(),
                'updated_at' => DT::utcNow(),
                'block_count' => 0,
                'blocks' => [],
                'deleted_at' => null,
                'email' => 'support@aparlay.com',
                'email_verified' => true,
                'followed_hashtag_count' => 0,
                'followed_hashtags' => [],
                'follower_count' => 0,
                'followers' => [],
                'following_count' => 0,
                'followings' => [],
                'full_name' => 'Alua Support',
                'gender' => 1,
                'interested_in' => 1,
                'like_count' => 0,
                'likes' => [],
                'media_count' => 0,
                'medias' => [],
                'phone_number' => null,
                'phone_number_verified' => false,
                'promo_link' => null,
                'visibility' => 1,
                'bio' => '',
                'avatar' => 'https://acdn.waptap.dev/avatars/default_m_42.png',
                'features' => [
                    'tips' => false,
                    'exclusive_content' => false,
                    'wallet_bank' => false,
                    'wallet_paypal' => false,
                    'wallet_cryptocurrency' => false,
                    'demo' => false,
                ],
                'remember_token' => 'EJ3bqsc7cRC7hLulCtXtPYJGHsUCzfmBq5ufFIvTCv2S3tqUKhZ551jYulNp',
                'role_ids' => [
                    '616e99cb664fda735460ca13',
                ],
                'updated_by' => new ObjectId('60489a4a99e10649054f9512'),
                'stats' => [
                    'amounts' => [
                        'sent_tips' => 0,
                        'received_tips' => 0,
                        'subscriptions' => 0,
                        'subscribers' => 0,
                    ],
                    'counters' => [
                        'followers' => 0,
                        'followings' => 0,
                        'likes' => 0,
                        'blocks' => 0,
                        'followed_hashtags' => 0,
                        'medias' => 0,
                        'subscriptions' => 0,
                        'subscribers' => 0,
                    ],
                ],
                'user_agents' => [],
                'text_search' =>
                    [
                        0 => '',
                        1 => 'support',
                        2 => 'support@aparlay.com',
                        3 => null,
                    ],
                'verification_status' => 3,
                'country_alpha2' => 'us',
                'last_online_at' => DT::utcNow(),
                'payout_country_alpha2' => 'us',
                'scores' => [
                    'sort' => 0,
                    'risk' => 0,
                ],
            ]);
        }

        foreach (Message::query()->where('created_by', null)->lazy() as $message) {
            $message->update(['created_by' => new ObjectId($aluaSupport->_id), 'created_at' => DT::utcNow()]);

            $lastMessage = $message->chatObj->last_message;
            if ((string)$lastMessage['_id'] === (string)$message->_id) {
                $participants = $message->chatObj->participants;
                $participants[] = [
                    '_id' => new ObjectId($aluaSupport->_id),
                    'username' => $aluaSupport->username,
                    'avatar' => $aluaSupport->avatar,
                    'is_verified' => $aluaSupport->is_verified,
                    'categories' => [ChatCategory::SUPPORT->value],
                    'status' => [ChatStatus::ACTIVE->value],
                    'has_unread_message' => false,
                    'joined_at' => DT::utcNow(),
                    'last_visited_at' => DT::utcNow(),
                ];

                $lastMessage['created_by'] = new ObjectId($aluaSupport->_id);
                $lastMessage['created_at'] = DT::utcNow();

                $message->chatObj->update([
                    'participants' => $participants,
                    'last_message' => $lastMessage
                ]);
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
