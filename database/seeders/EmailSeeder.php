<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Models\Email;
use Illuminate\Database\Seeder;

class EmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Email::factory()->count(20)->create();
    }
}
