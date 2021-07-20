<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Models\Version;
use Illuminate\Database\Seeder;

class VersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Version::factory()->count(100)->create();
    }
}
