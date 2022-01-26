<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Models\Note;
use Illuminate\Database\Seeder;

class NoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Note::factory()->count(20)->create();
    }
}
