<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\MediaComment;
use Aparlay\Core\Models\MediaLike;
use Aparlay\Core\Models\User;
use Aparlay\Payout\Models\Enums\PayerType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use MongoDB\BSON\ObjectId;

class MediaCommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $users = User::query()->limit(5)->get();
        $medias = User::query()->limit(10)->get();

        MediaComment::factory()
            ->count(50)
            ->state(function (array $attributes) use ($users, $medias) {
                return [
                    'media_id' => new ObjectId($medias->random()->_id),
                    'user_id' => new ObjectId($users->random()->_id),
                ];
            })
            ->create();

        foreach (MediaComment::lazy() as $mediaComment) {
            MediaComment::factory()
                ->count(rand(3, 5))
                ->state(function (array $attributes) use ($mediaComment, $users, $medias) {
                    return [
                        'media_id' => new ObjectId($medias->random()->_id),
                        'user_id' => new ObjectId($users->random()->_id),
                        'parent_id' => new ObjectId($mediaComment->_id),
                    ];
                })
                ->create();
        }
    }
}
