<?php

namespace Aparlay\Core\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
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
