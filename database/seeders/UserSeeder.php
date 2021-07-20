<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        User::factory(100)->create();
    }
}
