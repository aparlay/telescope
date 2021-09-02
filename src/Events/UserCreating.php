<?php

namespace Aparlay\Core\Events;

use Aparlay\Core\Helpers\Cdn;
use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserCreating
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return false|void
     * @throws \Exception
     */
    public function __construct(User $user)
    {
        $user->setting = config('app.user.defaultSetting');
        $now = DT::utcNow();
        $user->count_fields_updated_at = [
            'followers' => $now,
            'followings' => $now,
            'blocks' => $now,
            'likes' => $now,
            'visits' => $now,
            'medias' => $now,
            'followed_hashtags' => $now,
        ];
        if (empty($user->avatar)) {
            $filename = match ($user->gender) {
                User::GENDER_FEMALE => 'default_fm_'.random_int(1, 60).'.png',
                User::GENDER_MALE => 'default_m_'.random_int(1, 120).'.png',
                default => (
                    (random_int(0, 1)) ? 'default_m_'.random_int(1, 120) : 'default_fm_'.random_int(1, 60)
                    ).'.png',
            };

            $user->avatar = Cdn::avatar($filename);
        }
    }
}
