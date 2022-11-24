<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Models\ActiveUser;
use Illuminate\Database\Seeder;

class ActiveUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        ActiveUser::factory()->count(300000)->create();
    }
}
