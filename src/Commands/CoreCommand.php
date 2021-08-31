<?php

namespace Aparlay\Core\Commands;

use Illuminate\Console\Command;

class CoreCommand extends Command
{
    public $signature = 'core:index';

    public $description = 'Aparlay Core Command';

    public function handle()
    {
        $this->comment('All done');
    }
}
