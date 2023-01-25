<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\User;
use Illuminate\Console\Command;
use Illuminate\Http\Response;

class UserScoreDailyCommand extends Command
{
    public $signature = 'user:score-daily';

    public $description = 'This command is responsible for update user score daily';

    public function handle()
    {
        Media::where('is_fake', ['$exists' => false])
            ->date(DT::utcDateTime(['d' => -6]), DT::utcDateTime(['d' => -1]))
            ->availableForFollower()
            ->each(function ($media) {
                $user = $media->creatorObj;
                $scores = $user->scores;
                $oldScore = $scores['sort'];
                $scores['sort'] = 0;

                $count = Media::creator($user->_id)->availableForOwner()->count();
                if ($count > 0) {
                    $score = Media::creator($user->_id)->availableForOwner()->sum('sort_scores.default');
                    $scores['sort'] = $score / $count;
                }
                $user->scores = $scores;

                if ($oldScore != $scores['sort']) {
                    $msg5 = '<fg=yellow;options=bold>';
                    $msg5 .= '  - total set to '.$user->scores['sort'].'</>';
                    $msg5 .= PHP_EOL;
                    $this->line($msg5);

                    $user->update(['scores' => $scores]);
                }
            });

        return self::SUCCESS;
    }
}
