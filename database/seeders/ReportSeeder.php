<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\Report;
use Aparlay\Core\Models\User;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Report::factory()->count(20)
            ->for(User::factory()->create(), 'userObj')
            ->for(Media::factory()->forUserObj()->create(), 'mediaObj')
            ->create();
    }
}
