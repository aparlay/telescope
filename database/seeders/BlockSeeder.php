<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Models\Block;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::collection('blocks')->truncate();
        Block::factory()->count(100)->create();
    }
}
