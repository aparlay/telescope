<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Models\Analytic;
use Illuminate\Database\Seeder;

class AnalyticSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Analytic::factory()->count(100)->create();
    }
}
