<?php

namespace Aparlay\Core\Commands;

use Aparlay\Chat\Models\Chat;
use Aparlay\Core\Models\Block;
use Aparlay\Core\Models\Follow;
use Aparlay\Core\Models\Hashtag;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\MediaLike;
use Aparlay\Core\Models\User;
use Aparlay\Core\Models\UserNotification;
use Illuminate\Console\Command;
use MongoDB\BSON\ObjectId;

class ResetUserCountersCommand extends Command
{
    public $signature = 'counter:user';

    public $description = 'Aparlay Update User Counters';

    public function handle()
    {
        foreach (User::where('is_fake', ['$exists' => false])->lazy() as $user) {
            /** @var User $user */
            $user->fillStatsCountersField([
                'followers' => Follow::query()->user($user->_id)->accepted()->count() ?? 0,
                'followings' => Follow::query()->creator($user->_id)->accepted()->count() ?? 0,
                'likes' => MediaLike::query()->user($user->_id)->count() ?? 0,
                'blocks' => Block::query()->creator($user->_id)->count() ?? 0,
                'followed_hashtags' => count($user->followed_hashtags ?? []),
                'medias' => Media::query()->creator($user->_id)->availableForOwner()->count(),
                'subscriptions' => 0,
                'subscribers' => 0,
                'chats' => Chat::query()->participants($user->_id)->count() ?? 0,
                'notifications' => UserNotification::query()->user($user->_id)->count() ?? 0,
            ]);

            $user->saveQuietly();
            $user->updateLikes();
            $this->info('User '.$user->_id.' has been updated');
        }
        $this->comment('All done');

        return self::SUCCESS;
    }
}
