<?php

namespace Aparlay\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseTruncate extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $collections = [
            'alerts',
            'analytics',
            'blocks',
            'emails',
            'medias',
            'media_likes',
            'media_visits',
            'users',
            'versions',
            'user_documents',
            'user_notifications',
            'notes',
            'media_comments',
            'media_comment_likes',
        ];

        foreach ($collections as $collection) {
            DB::collection($collection)->truncate();
        }
    }
}
