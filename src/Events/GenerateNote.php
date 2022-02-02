<?php

namespace Aparlay\Core\Events;

use Aparlay\Core\Models\User;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 *
 */
class GenerateNote
{
    use Dispatchable;
    use SerializesModels;

    public Authenticated|User $creator;
    public User $user;
    public int $type;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Authenticated|User $creator, User $user, int $type) {
        $this->creator = $creator;
        $this->user = $user;
        $this->type = $type;
    }
}
