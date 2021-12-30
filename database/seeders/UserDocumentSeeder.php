<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\User;
use Aparlay\Core\Models\UserDocument;
use Illuminate\Database\Seeder;
use MongoDB\BSON\ObjectId;

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
        $user = User::factory()->create();

        UserDocument::factory()->count($this->units)
            ->state(function (array $attributes) use($user) {
                return [
                    'user' =>  [
                        '_id' => new ObjectId($user->_id),
                        'username' => $user->username,
                        'avatar' => $user->avatar,
                    ],
                    'crator' =>  [
                        '_id' => new ObjectId($user->_id),
                        'username' => $user->username,
                        'avatar' => $user->avatar,
                    ],
                ];
            })
            ->create();
    }
}
