<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Models\Analytic;
use Illuminate\Database\Seeder;

class AnalyticSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Analytic::factory()->count(30)->create();
    }
}
