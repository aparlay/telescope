<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Models\Media;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::collection('medias')->truncate();
        Media::factory()->count(100)->create();
    }
}
