<?php

namespace Aparlay\Core\Repositories;

class UserRepository
{
    public function verify(User $user) {
        $user->status = User::STATUS_VERIFIED;
        $user->email_verified = true;
        $user->save(['status', 'email_verified']);
    }
}
