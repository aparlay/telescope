<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Models\MediaLike;
use Illuminate\Database\Seeder;

class MediaLikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        MediaLike::factory()->count(1000)->create();
    }
}
