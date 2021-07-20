<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Models\Analytic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnalyticSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::collection('analytics')->truncate();
        Analytic::factory()->count(100)->create();
    }
}
