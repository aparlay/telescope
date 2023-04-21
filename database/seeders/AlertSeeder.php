<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Models\Alert;
use Illuminate\Database\Seeder;

class AlertSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Alert::factory()->count(20)->create();
    }
}
