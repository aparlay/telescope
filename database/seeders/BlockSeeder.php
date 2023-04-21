<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Models\Block;
use Illuminate\Database\Seeder;

class BlockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Block::factory()->count(20)->create();
    }
}
