<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\User;
use Illuminate\Console\Command;
use Illuminate\Http\Response;

class UserScoreCommand extends Command
{
    public $signature = 'user:score';

    public $description = 'This command is responsible for update user score';

    public function handle()
    {
        User::Where(['is_fake' => ['$exists' => false]])
            ->enable()
            ->each(function ($user) {
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
