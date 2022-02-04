<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Models\Media;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Media::unsetEventDispatcher();

        DB::collection('alerts')->truncate();
        DB::collection('analytics')->truncate();
        DB::collection('blocks')->truncate();
        DB::collection('emails')->truncate();
        DB::collection('medias')->truncate();
        DB::collection('media_likes')->truncate();
        DB::collection('media_visits')->truncate();
        DB::collection('users')->truncate();
        DB::collection('versions')->truncate();
        DB::collection('user_documents')->truncate();
        DB::collection('notes')->truncate();

        $this->call([
            AlertSeeder::class,
            AnalyticSeeder::class,
            BlockSeeder::class,
            EmailSeeder::class,
            FollowSeeder::class,
            MediaSeeder::class,
            MediaLikeSeeder::class,
            MediaVisitSeeder::class,
            UserSeeder::class,
            UserDocumentSeeder::class,
            VersionSeeder::class,
            NoteSeeder::class,
        ]);
    }
}
