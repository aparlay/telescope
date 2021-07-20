<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Models\Email;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::collection('emails')->truncate();
        Email::factory()->count(100)->create();
    }
}
