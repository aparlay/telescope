<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Models\Follow;
use Illuminate\Database\Seeder;

class FollowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Follow::factory()->count(20)->create();
    }
}
