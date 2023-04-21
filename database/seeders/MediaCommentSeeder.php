<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Api\V1\Resources\MediaCommentResource;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\MediaComment;
use Aparlay\Core\Models\User;
use Illuminate\Database\Seeder;
use MongoDB\BSON\ObjectId;

class MediaCommentSeeder extends Seeder
{
    public function __construct()
    {
        MediaComment::query()->truncate();
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users                  = User::query()->limit(5)->get();
        $medias                 = Media::query()->limit(10)->get();

        $this->command->getOutput()->block('Creating media comments' . "\n\r");
        $mediaCommentBar        = $this->command->getOutput()->createProgressBar(50);

        MediaComment::factory()
            ->count(50)
            ->state(function (array $attributes) use ($users, $medias, $mediaCommentBar) {
                $mediaCommentBar->advance();

                return [
                    'media_id' => new ObjectId($medias->random()->_id),
                    'user_id' => new ObjectId($users->random()->_id),
                ];
            })
            ->create();

        $mediaCommentBar->finish();

        $this->command->getOutput()->block("\n\r");
        $this->command->getOutput()->block('Creating media comments replies' . "\n\r");
        $mediaCommentRepliesBar = $this->command->getOutput()->createProgressBar(50);

        foreach (MediaComment::lazy() as $mediaComment) {
            $rand                        = rand(3, 11);
            $mediaReplies                = MediaComment::factory()
                ->count($rand)
                ->state(function (array $attributes) use ($mediaComment, $users, $medias) {
                    $mediaComment->load('creatorObj');
                    $replyToUser = $mediaComment->creatorObj;

                    return [
                        'media_id' => new ObjectId($medias->random()->_id),
                        'user_id' => new ObjectId($users->random()->_id),
                        'reply_to_user' => [
                            '_id' => new ObjectId($replyToUser->_id),
                            'username' => $replyToUser->username,
                            'avatar' => $replyToUser->avatar,
                        ],
                        'parent' => [
                            '_id' => new ObjectId($mediaComment->_id),
                        ],
                    ];
                })
                ->create();

            $firstReply                  = $mediaReplies[0];
            $firstReply->is_first        = true;
            $firstReply->save();

            $mediaComment->first_reply   = (new MediaCommentResource($firstReply))->resolve();
            $mediaCommentRepliesBar->advance();
            $mediaComment->replies_count = $rand;
            $mediaComment->save();
        }

        $mediaCommentRepliesBar->finish();
        $this->command->getOutput()->block("\n\r");
    }
}
