<?php

namespace Aparlay\Core\Database\Seeders;

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
        DB::collection('alerts')->truncate();
        DB::collection('analytics')->truncate();
        DB::collection('blocks')->truncate();
        DB::collection('emails')->truncate();
        DB::collection('medias')->truncate();
        DB::collection('media_likes')->truncate();
        DB::collection('media_visits')->truncate();
        DB::collection('users')->truncate();
        DB::collection('versions')->truncate();
        DB::collection('orders')->truncate();
        DB::collection('transactions')->truncate();
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
            VersionSeeder::class,
        ]);
    }
}
