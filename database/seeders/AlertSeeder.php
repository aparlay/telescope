<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Models\Alert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AlertSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::collection('alerts')->truncate();
        Alert::factory()->count(100)->create();
    }
}
