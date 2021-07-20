<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Models\MediaLike;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MediaLikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::collection('media_likes')->truncate();
        MediaLike::factory()->count(1000)->create();
    }
}
