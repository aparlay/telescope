<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Models\BlackList;
use Aparlay\Core\Models\User;
use Illuminate\Console\Command;
use Illuminate\Notifications\Messages\SlackMessage;

class UserSuspiciousCommand extends Command
{
    public $signature = 'user:suspicious';

    public $description = 'This command is responsible for update user score';

    public function handle()
    {
        foreach (BlackList::query()->temporaryEmailService()->get() as $blackList) {
            User::enable()
                ->where('email', 'regexp', '/.*@'.$blackList->payload.'/i')
                ->each(function ($user) {
                    return (new \Illuminate\Notifications\Messages\SlackMessage())
                        ->to(config('app.slack_report'))
                        ->content($user->slack_url)
                        ->success();
                });
        }


        return self::SUCCESS;
    }
}
