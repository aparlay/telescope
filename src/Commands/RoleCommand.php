<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Admin\Models\User;
use Illuminate\Console\Command;
use Maklad\Permission\Models\Role;

class RoleCommand extends Command
{
    public $signature   = 'core:role {email} {role}';
    public $description = 'Assign role to user';

    public function handle()
    {
        $user = User::where('email', $this->argument('email'))->firstOrFail();
        $role = Role::where('name', $this->argument('role'))->firstOrFail();

        $user->assignRole($role);

        return self::SUCCESS;
    }
}
