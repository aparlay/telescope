<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Models\Report;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::collection('reports')->truncate();
        Report::factory()->count(100)->create();
    }
}
