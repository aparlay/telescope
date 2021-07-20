<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Models\Version;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::collection('versions')->truncate();
        Version::factory()->count(100)->create();
    }
}
