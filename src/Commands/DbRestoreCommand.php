<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Analytic;
use Aparlay\Core\Models\Email;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\MediaLike;
use Aparlay\Core\Models\MediaVisit;
use Illuminate\Console\Command;

class DbRestoreCommand extends Command
{
    public $signature = 'db:restore {host=localhost : Database server IP address} {port=27017 : Database mongodb port number} {username?} {password?} {authSource=admin : Authentication Database} {database?} {--C|compress}';

    public $description = 'This command is responsible to restore backup from db';

    public function handle()
    {
        return self::SUCCESS;
    }
}
