<?php

namespace Aparlay\Core\Events;

use Aparlay\Core\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ServerAlarmEvent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        public string $server,
        public array $messages,
    ) {
    }
}