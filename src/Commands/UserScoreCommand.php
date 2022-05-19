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
                $count = Media::creator($user->_id)->count();
                if ($count > 0) {
                    $score = Media::creator($user->_id)->sum('sort_score');
                    $scores = $user->scores;
                    $scores['sort'] = $score / $count;

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
