<?php

namespace Aparlay\Core\Listeners;

use Aparlay\Core\Models\Enums\UserWsState;
use Aparlay\Core\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Str;

class SocketClientStateEventListener implements ShouldQueue
{
    public function handle($event)
    {
        $state  = $event->payload['data']['state'] ?? '';
        if (!in_array($state, UserWsState::getAllValues())) {
            return;
        }

        $userId = Str::replace('private-users.', '', $event->payload['channel'] ?? '');

        if (empty($userId)) {
            return;
        }

        if (($user = User::query()->user($userId)->first()) !== null && $user->ws_state !== $state) {
            $user->ws_state = $state;
            $user->saveQuietly();
        }
    }
}
