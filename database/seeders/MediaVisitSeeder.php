<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Models\MediaVisit;
use Illuminate\Database\Seeder;

class MediaVisitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        MediaVisit::factory(1000)->create();
    }
}
