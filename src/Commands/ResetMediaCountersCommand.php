<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\MediaComment;
use Aparlay\Core\Models\MediaLike;
use Aparlay\Core\Models\MediaVisit;
use Illuminate\Console\Command;
use MongoDB\BSON\ObjectId;

class ResetMediaCountersCommand extends Command
{
    public $signature = 'counter:media';

    public $description = 'Aparlay Update Media Counters';

    public function handle()
    {
        foreach (Media::lazy() as $media) {
            /** @var Media $media */
            $likes = [];
            foreach (MediaLike::query()->media($media->_id)->recentFirst()->limit(10)->get() as $mediaLike) {
                $likes[] = [
                    '_id' => new ObjectId($mediaLike->creator['_id']),
                    'username' => $mediaLike->creator['username'],
                    'avatar' => $mediaLike->creator['avatar'],
                ];
            }
            $comments = [];
            foreach (MediaComment::query()->media($media->_id)->recentFirst()->limit(10)->get() as $mediaComment) {
                $comments[] = [
                    '_id' => new ObjectId($mediaComment->creator['_id']),
                    'username' => $mediaComment->creator['username'],
                    'avatar' => $mediaComment->creator['avatar'],
                ];
            }
            $media->fill([
                'like_count' => MediaLike::query()->media($media->_id)->count(),
                'visit_count' => MediaVisit::query()->media($media->_id)->count(),
                'comment_count' => MediaComment::query()->media($media->_id)->count(),
                'likes' => $likes,
                'comments' => $comments,
            ]);
            $media->saveQuietly();
        }
        $this->comment('All done');

        return self::SUCCESS;
    }
}
