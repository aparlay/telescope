<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Models\Follow;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FollowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::collection('followers')->truncate();
        Follow::factory()->count(100)->create();
    }
}
