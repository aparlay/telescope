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
        MediaComment::factory()
            ->count(50)
            ->for(User::factory()->create(), 'userObj')
            ->for(Media::factory()->for(User::factory()->create(), 'userObj')->create(), 'mediaObj')
            ->create();

        foreach (MediaComment::lazy() as $mediaComment) {
            MediaComment::factory()
                ->count(rand(3, 5))
                ->for(User::factory()->create(), 'userObj')
                ->for(Media::factory()->for(User::factory()->create(), 'userObj')->create(), 'mediaObj')
                ->state(function (array $attributes) use ($mediaComment) {
                    return [
                        'parent_id' => new ObjectId($mediaComment->_id),
                    ];
                })
                ->create();
        }
    }
}
