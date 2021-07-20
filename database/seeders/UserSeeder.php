<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::collection('users')->truncate();
        User::factory()->count(100)->create();
    }
}
