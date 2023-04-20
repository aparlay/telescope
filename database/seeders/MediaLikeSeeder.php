<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\MediaLike;
use Aparlay\Core\Models\User;
use Illuminate\Database\Seeder;

class MediaLikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MediaLike::factory()
            ->count(20)
            ->for(User::factory()->create(), 'userObj')
            ->for(Media::factory()->for(User::factory()->create(), 'userObj')->create(), 'mediaObj')
            ->create();
    }
}
