<?php

namespace Aparlay\Core\Events;

use Aparlay\Core\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SocketClientEvent
{
    use Dispatchable;
    use SerializesModels;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(public array $payload)
    {
    }
}
