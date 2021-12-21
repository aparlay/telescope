<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\User;
use Aparlay\Core\Models\UserDocument;
use Illuminate\Database\Seeder;

class UserDocumentSeeder extends Seeder
{
    protected $units = 20;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        UserDocument::factory()->count($this->units)
            ->for(User::factory()->create(), 'userObj')
            ->create();
    }
}
