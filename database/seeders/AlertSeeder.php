<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Models\Alert;
use Illuminate\Database\Seeder;

class AlertSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        \Aparlay\Core\Models\Alert::factory()->count(100)->create();
    }
}
