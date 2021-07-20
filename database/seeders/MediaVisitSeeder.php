<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Models\MediaVisit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MediaVisitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::collection('media_visits')->truncate();
        MediaVisit::factory()->count(1000)->create();
    }
}
