<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Models\Follow;
use Illuminate\Database\Seeder;

class FollowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Follow::factory()->count(100)->create();
    }
}
