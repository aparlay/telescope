<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\User;
use Illuminate\Console\Command;

class UserScoreCommand extends Command
{
    public $signature   = 'user:score';
    public $description = 'This command is responsible for update user score';

    public function handle()
    {
        $userQuery = User::enable()->whereNull('is_fake');
        $bar       = $this->output->createProgressBar($userQuery->count());
        foreach ($userQuery->lazy() as $user) {
            $scores         = $user->scores;
            $oldScore       = $scores['sort'];
            $scores['sort'] = 0;

            $count          = Media::creator($user->_id)->availableForOwner()->count();
            if ($count > 0) {
                $score          = Media::creator($user->_id)->availableForOwner()->sum('sort_scores.default');
                $scores['sort'] = $score / $count;
            }
            $user->scores   = $scores;

            if ($oldScore != $scores['sort']) {
                $user->update(['scores' => $scores]);
            }
            $bar->advance();
        }

        $bar->finish();
        $this->line('');

        return self::SUCCESS;
    }
}
