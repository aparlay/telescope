<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Admin\Models\User as AdminUser;
use Aparlay\Core\Models\Enums\UserStatus;
use Aparlay\Core\Models\Enums\UserType;
use Aparlay\Core\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Maklad\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->count(20)->create();
        User::factory()->count(5)->create(['type' => UserType::ADMIN->value]);

        if (App::environment('local', 'testing')) {
            $this->createTestUser();
        }
    }

    public function createTestUser()
    {
        $user                = AdminUser::query()->first();

        $user->type          = UserType::ADMIN->value;
        $user->password_hash = Hash::make('waptap');
        $user->status        = UserStatus::ACTIVE->value;
        $user->email         = 'user@waptap.com';
        $user->save();
        $role                = Role::firstOrCreate(['name' => 'super-administrator', 'guard_name' => 'admin']);
        $user->assignRole('super-administrator');
    }
}
