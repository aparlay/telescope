<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Models\Block;
use Illuminate\Database\Seeder;

class BlockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Block::factory(100)->create();
    }
}
