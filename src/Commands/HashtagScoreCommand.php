<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Jobs\RecalculateHashtag;
use Aparlay\Core\Models\Hashtag;
use Aparlay\Core\Models\Media;
use Illuminate\Console\Command;

class HashtagScoreCommand extends Command
{
    public $signature = 'hashtag:score';

    public $description = 'This command is responsible for update hashtag score';

    public function handle()
    {
        $tags = [];
        Media::where('is_fake', ['$exists' => false])
            ->where('hashtags', ['$type' => 'array'])
            ->confirmed()
            ->public()
            ->each(function ($media) use (&$tags) {
                foreach ($media->hashtags as $tag) {
                    if (! isset($tags[$tag])) {
                        $tags[$tag] = [
                            'like_count' => 0,
                            'visit_count' => 0,
                            'media_count' => 0,
                            'sort_score' => 0,
                        ];
                    }
                }
            });

        foreach ($tags as $tag => $counts) {
            $hashtag = Hashtag::firstOrCreate(['tag' => $tag]);
            $hashtag->like_count = Media::hashtag($tag)->sum('like_count');
            $hashtag->visit_count = Media::hashtag($tag)->sum('visit_count');
            $hashtag->media_count = $count = Media::hashtag($tag)->count();
            $hashtag->sort_score = (Media::hashtag($tag)->sum('sort_scores.default') / $count);
            $hashtag->save();
            $msg5 = '<fg=yellow;options=bold>- adding new hashtag: #'.$tag.':</> '.$count.'</>'.PHP_EOL;
            $this->line($msg5);
            RecalculateHashtag::dispatch($tag);
        }

        return self::SUCCESS;
    }
}
