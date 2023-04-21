<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Models\Note;
use Illuminate\Database\Seeder;

class NoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Note::factory()->count(20)->create();
    }
}
