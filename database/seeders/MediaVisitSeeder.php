<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Models\MediaVisit;
use Aparlay\Core\Models\User;
use Illuminate\Database\Seeder;

class MediaVisitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MediaVisit::factory()->count(20)->for(User::factory()->create(), 'userObj')->create();
    }
}
